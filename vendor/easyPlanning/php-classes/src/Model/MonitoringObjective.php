<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class MonitoringObjective extends Model
{

    public function __construct()
    {
        $fields = array(
            "obj_id",
            "monobj_id",
            "monobj_dtcreation",
            "monobj_results",
            "monobj_problems",
            "monobj_action",
            "monobj_deadline"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_monitoring_objectives;");
    }

    public static function listObjectiveMonitoring($idobj)
    {
        $sql = new Sql();
        return $sql->select("SELECT * 
            FROM tb_monitoring_objectives a 
            WHERE a.obj_id=:OBJ ORDER BY a.monobj_dtcreation;", array(":OBJ"=>$idobj));
    }
    
    public static function listTargetResults($idtarget)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT
                a.target_id,
                a.target_name,
                a.target_value,
                a.target_baseline,
                a.target_dtstart,
                DATEDIFF(NOW(),a.target_dtstart) as dias_inicio,
                DATEDIFF(NOW(),a.target_dtfinish) as dias_fim,
                b.obj_id,
                b.obj_description,
                c.plan_id,
                c.plan_title,
                d.persp_id,
                d.persp_name,
                e.monobj_id,
                e.monobj_value,
                e.monobj_date,
                e.monobj_comment
            FROM tb_monitoring e
            INNER JOIN tb_targets a USING (target_id)
            INNER JOIN tb_objectives b USING (obj_id)
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE a.target_id=:ID ORDER BY e.monobj_date DESC;", array(":ID"=>$idtarget));
        if($results){
            $results = self::proccessResults($results);
        }
        return $results;
    }
    
    public function save(){
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_monitoring_objectives (
            obj_id,
            monobj_results,
            monobj_problems,
            monobj_action,
            monobj_deadline
        ) VALUES(
            :obj_id
            :monobj_results,
            :monobj_problems,
            :monobj_action,
            :monobj_deadline
        );", array(
            ":obj_id" => $this->getobj_id(),
            ":monobj_results" => $this->getmonobj_value(),
            ":monobj_problems" => $this->getmonobj_problems(),
            ":monobj_action" => $this->getmonobj_action(),
            ":monobj_deadline" => $this->getmonobj_deadline()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_monitoring_objectives a WHERE a.monobj_id=:id", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_monitoring_objectives SET 
                obj_id=:obj_id,
                monobj_results=:monobj_results,
                monobj_problems=:monobj_problems,
                monobj_action=:monobj_action,
                monobj_deadline=:monobj_deadline
            WHERE monobj_id=:monobj_id;", array(
                ":obj_id" => $this->getobj_id(),
                ":monobj_results" => $this->getmonobj_value(),
                ":monobj_problems" => $this->getmonobj_problems(),
                ":monobj_action" => $this->getmonobj_action(),
                ":monobj_deadline" => $this->getmonobj_deadline(),
                ":monobj_id" => $this->getmonobj_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_monitoring_objectives WHERE monobj_id=:id", array(
            ":id" => $this->getmonobj_id()
        ));
    }
    
}
?>