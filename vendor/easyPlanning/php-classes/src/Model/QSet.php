<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class QSet extends Model
{

    public function __construct()
    {
        $fields = array(
            "qset_id",
            "qset_name",
            "qset_description"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_questions_sets q ORDER BY q.qset_name");
    }

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_questions_sets (
            qset_name,
            qset_description
        ) VALUES(
            :name,
            :description
        )", array(
            ":name" => $this->getqset_name(),
            ":description" => $this->getqset_description()
        ));
    }

    public function get($qsetid)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_questions_sets q WHERE q.qset_id=:id", array(
            ":id" => $qsetid
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_questions_sets SET 
                qset_name=:name,
                qset_description=:description
            WHERE org_id=:id", array(
                ":name" => $this->getqset_name(),
                ":description" => $this->getqset_description(),
                ":id" => $this->getqset_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_organizations q WHERE q.qset_id=:id", array(
            ":id" => $this->getqset_id()
        ));
    }
}
?>