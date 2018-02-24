<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Plan extends Model
{

    public function __construct()
    {
        $fields = array(
            "plan_id",
            "org_id",
            "plan_dtcreation" . "plan_isopen",
            "plan_title",
            "plan_team",
            "plan_mission",
            "plan_vision",
            "plan_values"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_strategic_planning a 
            INNER JOIN tb_organizations b USING(org_id) 
            ORDER BY a.plan_title;");
    }

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_strategic_planning (
            org_id,
            plan_isopen,
            plan_title,
            plan_team,
            plan_mission,
            plan_vision,
            plan_values
        ) VALUES(
            :org_id,
            :plan_isopen,
            :plan_title,
            :plan_team,
            :plan_mission,
            :plan_vision,
            :plan_values
        )", array(
            ":org_id" => $this->getorg_id(),
            ":plan_isopen" => $this->getplan_isopen(),
            ":plan_title" => $this->getplan_title(),
            ":plan_team" => $this->getplan_team(),
            ":plan_mission" => $this->getplan_mission(),
            ":plan_vision" => $this->getplan_vision(),
            ":plan_values" => $this->getplan_values()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_strategic_planning a WHERE a.plan_id=:id", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_strategic_planning SET 
                org_id=:org_id,
                plan_isopen=:plan_isopen,
                plan_title=:plan_title,
                plan_team=:plan_team,
                plan_mission=:plan_mission,
                plan_vision=:plan_vision,
                plan_values=:plan_values
            WHERE plan_id=:plan_id", array(
            ":org_id" => $this->getorg_id(),
            ":plan_isopen" => $this->getplan_isopen(),
            ":plan_title" => $this->getplan_title(),
            ":plan_team" => $this->getplan_team(),
            ":plan_mission" => $this->getplan_mission(),
            ":plan_vision" => $this->getplan_vision(),
            ":plan_values" => $this->getplan_values(),
            ":plan_id" => $this->getplan_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_strategic_planning WHERE plan_id=:id", array(
            ":id" => $this->getplan_id()
        ));
    }
}
?>