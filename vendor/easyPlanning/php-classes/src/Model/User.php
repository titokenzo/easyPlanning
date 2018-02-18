<?php 
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;
use easyPlanning\Mailer;
use easyPlanning\Security;
use easyPlanning\SysConfig;

class User extends Model{
    const SESSION = "User";
    
    public static function login($login, $password){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users WHERE user_login=:LOGIN", array(":LOGIN"=>$login));
        
        if(count($results)===0){
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
        
        $data = $results[0];
        if(password_verify($password, $data["user_password"])===true){
            $user = new User();
            $user->setData($data);
            
            $_SESSION[User::SESSION] = $user->getValues();
            return $user;
        }else{
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
    }
    
    public static function verifyLogin(){
        if(
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["user_id"]>0 
        ){
            header("Location: /login");
            exit;
        }
    }
    
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }
    
    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * from tb_users u INNER JOIN tb_persons p USING(person_id) ORDER BY p.person_name");
    }
    
    public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:name, :login, :pass, :email, :phone, :isadmin)", array(
            ":name"=> $this->getperson_name(),
            ":login"=> $this->getuser_login(),
            ":pass"=> $this->getuser_password(),
            ":email"=> $this->getperson_email(),
            ":phone"=> $this->getperson_phone(),
            ":isadmin"=> $this->getuser_isadmin()
        ));
        
        //$this->setData($results[0]);
    }
    
    public function get($user_id){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users u INNER JOIN tb_persons p USING(person_id) WHERE u.user_id=:id", array(
            ":id"=>$user_id
        ));
        
        $this->setData($results[0]);
    }
    
    public function update(){
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_usersupdate_save(:id, :name, :login, :pass, :email, :phone, :isadmin)", array(
            ":id"=> $this->getuser_id(),
            ":name"=> $this->getperson_name(),
            ":login"=> $this->getuser_login(),
            ":pass"=> $this->getuser_password(),
            ":email"=> $this->getperson_email(),
            ":phone"=> $this->getperson_phone(),
            ":isadmin"=> $this->getuser_isadmin()
        ));
        
        //$this->setData($results[0]);
    }
    
    public function delete(){
        $sql=new Sql();
        $sql->query("CALL sp_users_delete(:id)", array(
            ":id"=>$this->getuser_id()
        ));
    }
    
    public static function getForgot($email){
        $sql=new Sql();
        $results = $sql->select("SELECT * FROM tb_persons p INNER JOIN tb_users USING(person_id) WHERE p.person_email=:email", array(
            ":email"=>$email
        ));
        if(count($results)===0){
            throw new \Exception("Não foi possível recuperar a senha");
        }else{
            $data = $results[0];
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :ip)", array(
                ":iduser"=>$data["user_id"],
                ":ip"=>$_SERVER["REMOTE_ADDR"]
            ));
            if(count($results2)===0){
                throw new \Exception("Não foi possível recuperar a senha");
            }else{
                $dataRecovery = $results2[0];
                $code = Security::secured_encrypt($dataRecovery["recovery_id"]);
                $link = SysConfig::SITE_URL . "/forgot/reset?code=$code";
                $mailer = new Mailer($data["person_email"], $data["person_name"], "Redefinir senha do EasyPlanning", "forgot", array(
                    "name"=>$data["person_name"],
                    "link"=>$link
                ));
                $mailer->send();
                return $data;
            }
        }
    }
    
    public static function validForgotDecrypt($code){
        $idrecovery = Security::secured_decrypt_url($code);
        $sql = new Sql();
        $results = $sql->select("
            SELECT * 
            FROM tb_userspasswordsrecoveries r 
            INNER JOIN tb_users u USING(user_id)
            INNER JOIN tb_persons p USING(person_id)
            WHERE 
                r.recovery_dtrecovery IS NULL
                AND r.recovery_id=:idrecovery
                AND DATE_ADD(r.recovery_dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(
            ":idrecovery"=>$idrecovery
        ));
        if(count($results)===0){
            throw new \Exception("Não foi possível recuperar a senha");
        }else{
            return $results[0];
        }
    }
    
    public static function setForgotUsed($idrecovery){
        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET recovery_dtrecovery=NOW() WHERE recovery_id=:idrecovery",array(
            "idrecovery"=>$idrecovery
        ));
    }
    
    public function setPassword($pass){
        $sql = new Sql();
        $sql->query("UPDATE tb_users SET user_password=:pass WHERE user_id=:id", array(
            ":pass"=>$pass,
            ":id"=>$this->getuser_id()
        ));
    }
    
}
?>