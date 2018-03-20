<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;
use easyPlanning\Security;

class Diagnostic extends Model
{

    public function __construct()
    {
        $fields = array(
            "diagnostic_id",
            "org_id",
            "diagnostic_status",
            "diagnostic_dtcreation",
            "diagnostic_dtfinished",
            "diagnostic_text"
        );
        $this->setAttrs($fields);
    }

    public static function listAllFromOrg($idorg)
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_diagnostics a WHERE a.org_id=:ORG ORDER BY a.diagnostic_dtcreation;", array(":ORG"=>$idorg));
    }

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_diagnostics (
            org_id,
            diagnostic_text
        ) VALUES(
            :org_id,
            :diagnostic_text
        );", array(
            ":org_id" => (int)$this->getorg_id(),
            ":diagnostic_text" => $this->getdiagnostic_text()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_diagnostics a WHERE a.diagnostic_id=:id;", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_diagnostics SET 
                diagnostic_text=:diagnostic_text,
                diagnostic_status=:diagnostic_status
            WHERE diagnostic_id=:diagnostic_id", array(
                ":diagnostic_text" => $this->getdiagnostic_text(),
                ":diagnostic_status" => (int)$this->getdiagnostic_status(),
                ":diagnostic_id" => (int)$this->getdiagnostic_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_diagnostics WHERE diagnostic_id=:id", array(
            ":id" => $this->getdiagnostic_id()
        ));
    }
    
    public static function getStatusList(){
        $list = array(
            0 => "Excluído",
            1 => "Ativo",
            2 => "Suspenso",
            3 => "Bloqueado",
            4 => "Encerrado"
        );
        return $list;
    }

    public static function validFormDecrypt($code)
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
}
?>