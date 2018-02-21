<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Perspective extends Model
{

    public function __construct()
    {
        $fields = array(
            "persp_id",
            "persp_name",
            "persp_color",
            "persp_description"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_perspectives a ORDER BY a.persp_name");
    }

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_perspectives (
            persp_name,
            persp_color,
            persp_description
        ) VALUES(
            :name,
            :color,
            :description
        )", array(
            ":name" => $this->getpersp_name(),
            ":color" => $this->getpersp_color(),
            ":description" => $this->getpersp_description()
        ));
    }

    public function get($perspid)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_perspectives a WHERE a.persp_id=:id", array(
            ":id" => $perspid
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_perspectives SET 
                persp_name=:name,
                persp_color=:color,
                persp_description=:description
            WHERE persp_id=:id", array(
                ":name" => $this->getpersp_name(),
                ":color" => $this->getpersp_color(),
                ":description" => $this->getpersp_description(),
                ":id" => $this->getpersp_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_perspectives WHERE persp_id=:id", array(
            ":id" => $this->getpersp_id()
        ));
    }
}
?>