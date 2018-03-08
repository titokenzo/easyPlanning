<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;
use easyPlanning\Mailer;
use easyPlanning\Security;
use easyPlanning\SysConfig;

class User extends Model
{

    public const SESSION = "Logged";

    public function __construct()
    {
        $fields = array(
            "user_id",
            "user_login" . "user_password",
            "user_isadmin",
            "user_dtcreation",
            "user_dtupdate",
            "user_cpf",
            "user_name",
            "user_email",
            "user_islogged",
            "user_phone",
            "user_position",
            "user_photo",
            "user_dtlastlogin"
        );
        $this->setAttrs($fields);
    }

    public static function login($login, $password)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT user_id, user_name, user_login, user_password, user_isadmin FROM tb_users WHERE user_login=:LOGIN", array(
            ":LOGIN" => $login
        ));
        if (count($results) === 0) {
            throw new \Exception("Usuário inexistente, ou senha inválida");
        }
        $data = $results[0];
        if (password_verify($password, $data["user_password"]) === FALSE) {
            //throw new \Exception("Usuário inexistente ou senha inválida - " . $login ."/". $password . "=" . $data["user_password"]);
            throw new \Exception("Usuário inexistente ou senha inválida");
        }
        $user = new User();
        $data["user_password"] = NULL;
        $data["org_id"] = NULL;
        $data["org_name"] = NULL;
        // $user->setData($data);
        
        // GET USER ORGANIZATIONS
        $results = $sql->select("SELECT b.org_id, b.org_tradingname as org_name, a.userorg_type FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:ID", array(
            ":ID" => $data["user_id"]
        ));
        if((int) $data["user_isadmin"] === 0){
            if ((! $results) or count($results) === 0) {
                throw new \Exception("Usuário não associado a nenhuma Organização");
            } elseif (count($results) === 1) {
                $data = array_merge($data, $results[0]);
            }
        }
        
        $_SESSION[User::SESSION] = $data;
        // $_SESSION[User::SESSION] = $user->getValues();
        // return $user;
    }

    public static function setSessionOrganization($idorg)
    {
        $data = $_SESSION[User::SESSION];
        if ((int) $data["user_isadmin"] === 1 and (int) $idorg === 0) {
            $results = array(
                0 => array(
                    "org_id" => 0,
                    "org_name" => "Adminstração",
                    "userorg_type" => 0
                )
            );
        } else {
            $sql = new Sql();
            $results = $sql->select("SELECT b.org_id, b.org_tradingname as org_name, userorg_type FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:USER AND a.org_id=:ORG", array(
                ":USER" => $data["user_id"],
                ":ORG" => $idorg
            
            ));
        }
        if (count($results) > 0) {
            $_SESSION[User::SESSION] = array_merge($data, $results[0]);
        }
    }

    public static function verifyLogin()
    {
        if (! isset($_SESSION[User::SESSION]) || ! $_SESSION[User::SESSION] || ! (int) $_SESSION[User::SESSION]["user_id"] > 0) {
            header("Location: /login");
            exit();
        }
    }

    public static function logout()
    {
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_users a ORDER BY a.user_name");
    }

    public function save()
    {
        $pass = password_hash($this->getuser_password(), PASSWORD_DEFAULT, [
            "cost" => 12
        ]);
        
        $sql = new Sql();
        $results = $sql->query("INSERT INTO tb_users (
            user_login,
            user_password,
            user_isadmin,
            user_cpf,
            user_name,
            user_email,
            user_type,
            user_phone,
            user_position,
            user_photo
        ) VALUES(
            :user_login,
            :user_password,
            :user_isadmin,
            :user_cpf,
            :user_name,
            :user_email,
            :user_type,
            :user_phone,
            :user_position,
            :user_photo
        )", array(
            ":user_login" => $this->getuser_login(),
            ":user_password" => $pass,
            ":user_isadmin" => $this->getuser_isadmin(),
            ":user_cpf" => $this->getuser_cpf(),
            ":user_name" => $this->getuser_name(),
            ":user_email" => $this->getuser_email(),
            ":user_type" => $this->getuser_type(),
            ":user_phone" => $this->getuser_phone(),
            ":user_position" => $this->getuser_position(),
            ":user_photo" => $this->getuser_photo()
        ));
    }

    public function get($user_id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a WHERE a.user_id=:id", array(
            ":id" => $user_id
        ));
        if(!$results){
            throw new \Exception("Não foi possível recuperar o registro");
        }
        $this->setData($results[0]);
    }

    public function update()
    {
        $pass = password_hash($this->getuser_password(), PASSWORD_DEFAULT, [
            "cost" => 12
        ]);
        
        $sql = new Sql();
        $results = $sql->query("UPDATE tb_users SET 
            user_login=:user_login,
            user_password=:user_password,
            user_isadmin=:user_isadmin,
            user_cpf=:user_cpf,
            user_name=:user_name,
            user_email=:user_email,
            user_type=:user_type,
            user_phone=:user_phone,
            user_position=:user_position,
            user_photo=:user_photo,
            user_dtupdate=NOW()
        WHERE user_id=:user_id", array(
            ":user_login" => $this->getuser_login(),
            ":user_password" => $pass,
            ":user_isadmin" => $this->getuser_isadmin(),
            ":user_cpf" => $this->getuser_cpf(),
            ":user_name" => $this->getuser_name(),
            ":user_email" => $this->getuser_email(),
            ":user_type" => $this->getuser_type(),
            ":user_phone" => $this->getuser_phone(),
            ":user_position" => $this->getuser_position(),
            ":user_photo" => $this->getuser_photo(),
            ":user_id" => $this->getuser_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_users WHERE user_id=:id", array(
            ":id" => $this->getuser_id()
        ));
    }

    public static function getForgot($email)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a WHERE a.user_email=:email", array(
            ":email" => $email
        ));
        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha");
        } else {
            $data = $results[0];
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :ip)", array(
                ":iduser" => $data["user_id"],
                ":ip" => $_SERVER["REMOTE_ADDR"]
            ));
            if (count($results2) === 0) {
                throw new \Exception("Não foi possível recuperar a senha");
            } else {
                $dataRecovery = $results2[0];
                $code = Security::secured_encrypt($dataRecovery["recovery_id"]);
                $link = SysConfig::SITE_URL . "/forgot/reset?code=$code";
                $mailer = new Mailer($data["user_email"], $data["user_name"], "Redefinir senha do EasyPlanning", "forgot", array(
                    "name" => $data["user_name"],
                    "link" => $link
                ));
                try{
                    $mailer->send();
                }catch(\Exception $e){
                    throw new \Exception('Não foi possível enviar o e-mail de recuperação: ' . $e->getMessage());
                }
                // $mailer->send();
                return $data;
            }
        }
    }

    public static function validForgotDecrypt($code)
    {
        $idrecovery = Security::secured_decrypt_url($code);
        $sql = new Sql();
        $results = $sql->select("
            SELECT * 
            FROM tb_userspasswordsrecoveries r 
            INNER JOIN tb_users u USING(user_id)
            WHERE 
                r.recovery_dtrecovery IS NULL
                AND r.recovery_id=:idrecovery
                AND DATE_ADD(r.recovery_dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(
            ":idrecovery" => $idrecovery
        ));
        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha");
        } else {
            return $results[0];
        }
    }

    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET recovery_dtrecovery=NOW() WHERE recovery_id=:idrecovery", array(
            "idrecovery" => $idrecovery
        ));
    }

    public function setPassword($pass)
    {
        $pass = password_hash($pass, PASSWORD_DEFAULT, [
            "cost" => 12
        ]);
        $sql = new Sql();
        $sql->query("UPDATE tb_users SET user_password=:pass, user_dtupdate=NOW() WHERE user_id=:id", array(
            ":pass" => $pass,
            ":id" => $this->getuser_id()
        ));
    }

    public static function getUserTypeList()
    {
        $list = array(
            1 => "Consultor Interno",
            2 => "Colaborador"
        );
        return $list;
    }
    
    public static function getUserOrganizations($id)
    {
        $sql = new Sql();
        return $sql->select("SELECT b.user_id, a.org_id, b.userorg_type, a.org_tradingname FROM tb_organizations a LEFT JOIN tb_users_organizations b on a.org_id=b.org_id and b.user_id=:USER",array(":USER"=>$id));
    }
    
    public function registrar(){
        $sql = new Sql();
        $sql->query("INSERT INTO tb_tmp (data) VALUES(NOW())");
    }
    
    public function updatePermition($idorg, $type){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_users_organizations WHERE org_id=:ORG AND user_id=:USER",array(
            ":ORG" => $idorg,
            ":USER" => $this->getuser_id()
        ));
        if(!$type==0){
            $sql->query("INSERT INTO tb_users_organizations (org_id,user_id,userorg_type) VALUES (:ORG,:USER,:TYPE)",array(
                ":ORG" => $idorg,
                ":USER" => $this->getuser_id(),
                ":TYPE" => $type
            ));
        }
    }

    public static function getSessionUserOrganizations()
    {
        $id = (int) $_SESSION[User::SESSION]["user_id"];
        $sql = new Sql();
        return $sql->select("SELECT b.org_id, b.org_tradingname FROM tb_users_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.user_id=:USER", array(
            ":USER" => $id
        ));
    }
}
?>