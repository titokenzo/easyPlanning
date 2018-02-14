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
}
?>