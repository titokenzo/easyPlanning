<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Objective extends Model
{

    public function __construct()
    {
        $fields = array(
            "obj_id",
            "plan_id",
            "persp_id",
            "user_id",
            "obj_description",
            "obj_positionx",
            "obj_positiony",
            "obj_sizex",
            "obj_sizey",
            "obj_dtcreation"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_objectives a 
            INNER JOIN tb_strategic_planning b USING(plan_id) 
            INNER JOIN tb_perspectives c USING(persp_id)  
            ORDER BY a.obj_description;");
    }

    public static function listObjectivesFromOrg($idorg)
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_objectives a
            INNER JOIN tb_strategic_planning b USING(plan_id)
            INNER JOIN tb_perspectives c USING(persp_id) 
            WHERE b.org_id=:ORG ORDER BY c.persp_name, a.obj_description;", array(":ORG"=>$idorg));
    }
    
    public static function listObjectivesFromActivePlan($idorg){
        $sql = new Sql();
       return $sql->select("SELECT * from tb_objectives a
            INNER JOIN tb_strategic_planning b USING(plan_id)
            INNER JOIN tb_perspectives c USING(persp_id) 
            WHERE b.org_id=:ORG AND b.plan_status IN (1,2) ORDER BY c.persp_name, a.obj_description;", array(":ORG"=>$idorg));
    }
    
    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_objectives (
            plan_id,
            persp_id,
            user_id,
            obj_description
        ) VALUES(
            :plan_id,
            :persp_id,
            :user_id,
            :obj_description
        );", array(
            ":plan_id" => $this->getplan_id(),
            ":persp_id" => $this->getpersp_id(),
            ":user_id" => $this->getuser_id(),
            ":obj_description" => $this->getobj_description()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_objectives a WHERE a.obj_id=:id", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_objectives SET 
                plan_id=:plan_id,
                persp_id=:persp_id,
                user_id=:user_id,
                obj_description=:obj_description
            WHERE obj_id=:obj_id", array(
                ":plan_id" => $this->getplan_id(),
                ":persp_id" => $this->getpersp_id(),
                ":user_id" => $this->getuser_id(),
                ":obj_description" => $this->getobj_description(),
                ":obj_id" => $this->getobj_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_objectives WHERE obj_id=:id", array(
            ":id" => $this->getobj_id()
        ));
    }
    
    public static function getObjDepends($obj){
        $sql = new Sql();
        return $sql->select("SELECT a.obj_id, a.obj_description, b.obj_id AS depends FROM tb_objectives a LEFT JOIN tb_objective_objective b ON a.obj_id=b.obj_dependson AND b.obj_id=:ID WHERE a.obj_id<>:ID;", array(
            ":ID"=>$obj
        ));
    }
    
}
?>