<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;
use easyPlanning\Mailer;
use easyPlanning\Security;
use easyPlanning\SysConfig;

class Respondent extends Model
{

    public const SESSION = "Logged";

    public function __construct()
    {
        $fields = array(
            "resp_id",
            "org_id",
            "resp_email",
            "resp_dtcreation",
            "resp_allowreturn",
            "resp_allowpartial",
            "resp_hasemailerror",
            "resp_hascompleted",
            "resp_orglevel",
            "resp_dtentered",
            "resp_dtfinished",
            "resp_lastip"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_respondents a INNER JOIN tb_organizations b USING(org_id) ORDER BY b.org_tradingname, a.respondent_email");
    }

    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_respondents a WHERE a.resp_email=:resp_email AND a.org_id=:org_id", array(
            ":resp_email" => $this->getresp_email(),
            ":org_id" => $this->getorg_id()
        ));
        if (!count($results) === 0) {
            throw new \Exception("O e-mail $this->getresp_email() já está cadastrado para a Oganização");
        } else {
            $results = $sql->select("CALL sp_respondent_create(:org_id, :resp_email, :resp_orglevel, :resp_allowreturn, :resp_allowpartial)", array(
                ":org_id" => $this->georg_id(),
                ":resp_email" => $this->getresp_email(),
                ":resp_orglevel" => $this->getresp_orglevel(),
                ":resp_allowreturn" => $this->getresp_allowreturn(),
                ":resp_allowpartial" => $this->getresp_allowpartial()
            ));
            if (count($results) === 0) {
                throw new \Exception("Não foi possível salvar os dados");
            } else {
                $data = $results[0];
                $code = Security::secured_encrypt($data["resp_id"]);
                $link = SysConfig::SITE_URL . "/respond?code=$code";
                $mailer = new Mailer($data["resp_email"], "Colaborador", "EasyPlanning - Questinário", "mailRespond", array(
                    "name" => "Colaborador",
                    "link" => $link
                ));
                $mailer->send();
                //return $data;
            }
        }
    }

    public static function getForgot($email)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_respondents a WHERE a.resp_email=:email", array(
            ":email" => $email
        ));
        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha");
        } else {
            $data = $results[0];
            $results2 = $sql->select("CALL sp_respspasswordsrecoveries_create(:idresp, :ip)", array(
                ":idresp" => $data["resp_id"],
                ":ip" => $_SERVER["REMOTE_ADDR"]
            ));
            if (count($results2) === 0) {
                throw new \Exception("Não foi possível recuperar a senha");
            } else {
                $dataRecovery = $results2[0];
                $code = Security::secured_encrypt($dataRecovery["recovery_id"]);
                $link = SysConfig::SITE_URL . "/forgot/reset?code=$code";
                $mailer = new Mailer($data["resp_email"], $data["resp_name"], "Redefinir senha do EasyPlanning", "forgot", array(
                    "name" => $data["resp_name"],
                    "link" => $link
                ));
                $mailer->send();
                return $data;
            }
        }
    }
    
    public function get($resp_id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_respondents a WHERE a.resp_id=:id", array(
            ":id" => $resp_id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        $results = $sql->query("UPDATE tb_respondents SET 
            resp_login=:resp_login,
            resp_password=:resp_password,
            resp_isadmin=:resp_isadmin,
            resp_cpf=:resp_cpf,
            resp_name=:resp_name,
            resp_email=:resp_email,
            resp_type=:resp_type,
            resp_phone=:resp_phone,
            resp_position=:resp_position,
            resp_photo=:resp_photo,
            resp_dtupdate=NOW()
        WHERE resp_id=:resp_id", array(
            ":resp_login" => $this->getresp_login(),
            ":resp_password" => $pass,
            ":resp_isadmin" => $this->getresp_isadmin(),
            ":resp_cpf" => $this->getresp_cpf(),
            ":resp_name" => $this->getresp_name(),
            ":resp_email" => $this->getresp_email(),
            ":resp_type" => $this->getresp_type(),
            ":resp_phone" => $this->getresp_phone(),
            ":resp_position" => $this->getresp_position(),
            ":resp_photo" => $this->getresp_photo(),
            ":resp_id" => $this->getresp_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_respondents WHERE resp_id=:id", array(
            ":id" => $this->getresp_id()
        ));
    }

    public static function validForgotDecrypt($code)
    {
        $idrecovery = Security::secured_decrypt_url($code);
        $sql = new Sql();
        $results = $sql->select("
            SELECT * 
            FROM tb_respspasswordsrecoveries r 
            INNER JOIN tb_resps u USING(resp_id)
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
        $sql->query("UPDATE tb_respspasswordsrecoveries SET recovery_dtrecovery=NOW() WHERE recovery_id=:idrecovery", array(
            "idrecovery" => $idrecovery
        ));
    }

    public function setPassword($pass)
    {
        $pass = password_hash($pass, PASSWORD_DEFAULT, [
            "cost" => 12
        ]);
        $sql = new Sql();
        $sql->query("UPDATE tb_resps SET resp_password=:pass WHERE resp_id=:id", array(
            ":pass" => $pass,
            ":id" => $this->getresp_id()
        ));
    }

    public static function getOrganizationLevelList()
    {
        $list = array(
            0 => "Estratégico",
            1 => "Tático",
            2 => "Operacional"
        );
        return $list;
    }

    public static function getFromOrganization($orgid)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_repondents a WHERE org_id=:ID ORDER BY resp_email", array(
            ":ID" => (int)$orgid
        ));
    }

    public static function getSessionUserOrganizations()
    {
        $id = (int) $_SESSION[User::SESSION]["resp_id"];
        $sql = new Sql();
        return $sql->select("SELECT b.org_id, b.org_tradingname FROM tb_resps_organizations a INNER JOIN tb_organizations b USING (org_id) WHERE a.resp_id=:USER", array(
            ":USER" => $id
        ));
    }
}
?>