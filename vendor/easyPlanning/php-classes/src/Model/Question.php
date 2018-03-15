<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Question extends Model
{

    public function __construct()
    {
        $fields = array(
            "quest_id",
            "persp_id",
            "qset_id".
            "quest_text",
            "quest_environment",
            "quest_status",
            "quest_critical_key",
            "quest_positiveswot_full",
            "quest_negativeswot_full",
            "quest_positiveswot",
            "quest_negativeswot",
            "quest_variable"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_questions a 
            INNER JOIN tb_questions_sets b USING(qset_id) 
            INNER JOIN tb_perspectives c USING(persp_id) 
            ORDER BY a.quest_text;"
        );
    }

    public static function listAllFromEnvironment($quest_environment=0)
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_questions a
            INNER JOIN tb_questions_sets b USING(qset_id)
            INNER JOIN tb_perspectives c USING(persp_id)
            WHERE a.quest_environment=:ENV
            ORDER BY a.quest_text;",array(":ENV"=>$quest_environment)
            );
    }
    
    public static function listDistinctlPerspectives($quest_environment)
    {
        $sql = new Sql();
        return $sql->select("SELECT DISTINCT c.persp_name,c.persp_id from tb_questions a
            INNER JOIN tb_perspectives c USING(persp_id)
            WHERE a.quest_environment=:ENV
            ORDER BY c.persp_name;", array(":ENV"=>$quest_environment)
            );
    }
    
    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_questions (
            persp_id,
            qset_id,
            quest_text,
            quest_environment,
            quest_status,
            quest_iscriticalkey,
            quest_positiveswot_full,
            quest_negativeswot_full,
            quest_positiveswot,
            quest_negativeswot,
            quest_variable
        ) VALUES(
            :persp_id,
            :qset_id,
            :quest_text,
            :quest_environment,
            :quest_status,
            :quest_iscriticalkey,
            :quest_positiveswot_full,
            :quest_negativeswot_full,
            :quest_positiveswot,
            :quest_negativeswot,
            :quest_variable
        )", array(
            ":persp_id" => $this->getpersp_id(),
            ":qset_id" => $this->getqset_id(),
            ":quest_text" => $this->getquest_text(),
            ":quest_environment" => $this->getquest_environment(),
            ":quest_status" => $this->getquest_status(),
            ":quest_iscriticalkey" => $this->getquest_iscriticalkey(),
            ":quest_positiveswot_full" => $this->getquest_positiveswot_full(),
            ":quest_negativeswot_full" => $this->getquest_negativeswot_full(),
            ":quest_positiveswot" => $this->getquest_positiveswot(),
            ":quest_negativeswot" => $this->getquest_negativeswot(),
            ":quest_variable" => $this->getquest_variable()
        ));
    }

    public function get($questid)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_questions a WHERE a.quest_id=:id", array(
            ":id" => $questid
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_questions SET 
                persp_id=:persp_id,
                qset_id=:qset_id,
                quest_text=:quest_text,
                quest_environment=:quest_environment,
                quest_status=:quest_status,
                quest_iscriticalkey=:quest_iscriticalkey,
                quest_positiveswot_full=:quest_positiveswot_full,
                quest_negativeswot_full=:quest_negativeswot_full,
                quest_positiveswot=:quest_positiveswot,
                quest_negativeswot=:quest_negativeswot,
                quest_variable=:quest_variable 
            WHERE quest_id=:quest_id", array(
                ":persp_id" => $this->getpersp_id(),
                ":qset_id" => $this->getqset_id(),
                ":quest_text" => $this->getquest_text(),
                ":quest_environment" => $this->getquest_environment(),
                ":quest_status" => $this->getquest_status(),
                ":quest_iscriticalkey" => $this->getquest_iscriticalkey(),
                ":quest_positiveswot_full" => $this->getquest_positiveswot_full(),
                ":quest_negativeswot_full" => $this->getquest_negativeswot_full(),
                ":quest_positiveswot" => $this->getquest_positiveswot(),
                ":quest_negativeswot" => $this->getquest_negativeswot(),
                ":quest_variable" => $this->getquest_variable(),
                ":quest_id" => $this->getquest_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_questions WHERE quest_id=:id", array(
            ":id" => $this->getquest_id()
        ));
    }
    
    public static function getStatusList()
    {
        $list = array(
            0 => "Inativo",
            1 => "Ativo"
        );
        return $list;
    }

    public static function getEnvironmentList()
    {
        $list = array(
            0 => "Interno",
            1 => "Externo"
        );
        return $list;
    }
}
?>