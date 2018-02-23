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
            throw new \Exception("Usuário inexistente, ou senha inválida");
        }
        $data = $results[0];
        if(password_verify($password, $data["user_password"])===FALSE){
            throw new \Exception("Usuário inexistente ou senha inválida");
        }
        $user = new User();
        $data["user_password"]=NULL;
        $data["org_id"] = NULL;
        //$user->setData($data);

        //GET USER ORGANIZATIONS
        $results = $sql->select("SELECT b.org_id, b.org_tradingname FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:ID", array(":ID"=>$data["user_id"]));
        if(count($results)===0 AND !(int)$data["user_isadmin"]===1){
            throw new \Exception("Usuário não associado a nenhuma Organização");
        }elseif(count($results)===1){
            $data = array_merge($data,$results[0]);
        }
        
        $_SESSION[User::SESSION] = $data;       
        //$_SESSION[User::SESSION] = $user->getValues();
        //return $user;
    }
    
    public static function setSessionOrganization($idorg){
        $data = $_SESSION[User::SESSION];
        if((int)$data["user_isadmin"]===1 AND (int)$idorg===0){
            $results = array(0=>array("org_id"=>0, "org_tradingname"=>"Adminstração"));
        }else{
            $sql = new Sql();
            $results = $sql->select("SELECT b.org_id, b.org_tradingname FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:USER AND a.org_id=:ORG", array(
                ":USER"=>$data["user_id"],
                ":ORG"=>$idorg
                
            ));
        }
        if(count($results)>0){
            $_SESSION[User::SESSION] = array_merge($data,$results[0]);
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
        if(count($results)>0){
            $this->setData($results[0]);
            
        }
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
    
    public static function getTypeList(){
        $list = array(
            0 => "Administrador",
            1 => "Consultor Interno",
            2 => "Colaborador"
        );
        return $list;
    }
    
    public static function getOrganizationList(){
        $sql = new Sql();
        return $sql->select("SELECT org_id, org_tradingname from tb_organizations ORDER BY org_tradingname");
    }
    
    public static function getSessionUserOrganizations(){
        $id = (int)$_SESSION[User::SESSION]["user_id"];
        $sql = new Sql();
        return $sql->select("SELECT b.org_id, b.org_tradingname FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:USER", array(":USER"=>$id));
    }
}
?>