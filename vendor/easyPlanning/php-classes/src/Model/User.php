<?php 
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

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
    
}
?>