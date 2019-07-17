<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Target extends Model
{

    public function __construct()
    {
        $fields = array(
            "obj_id",
            "target_id",
            "user_id",
            "target_code",
            "target_name",
            "target_description",
            "target_justify",
            "target_formula",
            "target_dtstart",
            "target_dtfinish",
            "target_frequence",
            "target_frequenceunit",
            "target_baseline",
            "target_value",
            "target_unit",
            "target_weight",
            "target_status",
            "target_methodology"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_targets a ORDER BY a.target_name;");
    }

    public static function listObjectiveTargets($idobj)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_targets a WHERE a.obj_id=:OBJ ORDER BY a.target_name;", array(":OBJ"=>$idobj));
    }
    
    public static function listPlanningTargets($idplan)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_targets a 
            INNER JOIN tb_objectives b USING (obj_id) 
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE c.plan_id=:ID ORDER BY d.persp_name, b.obj_description, a.target_name;", array(":ID"=>$idplan));
    }
    
    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_targets (
            obj_id,
            user_id,
            target_name,
            target_code,
            target_description,
            target_justify,
            target_methodology,
            target_formula,
            target_unit,
            target_baseline,
            target_value,
            target_dtstart,
            target_dtfinish,
            target_frequence,
            target_frequenceunit
        ) VALUES(
            :obj_id,
            :user_id,
            :target_name,
            :target_code,
            :target_description,
            :target_justify,
            :target_methodology,
            :target_formula,
            :target_unit,
            :target_baseline,
            :target_value,
            :target_dtstart,
            :target_dtfinish,
            :target_frequence,
            :target_frequenceunit
        );", array(
            ":obj_id" => $this->getobj_id(),
            ":user_id" => $this->getuser_id(),
            ":target_name" => $this->gettarget_name(),
            ":target_code" => $this->gettarget_code(),
            ":target_description" => $this->gettarget_description(),
            ":target_justify" => $this->gettarget_justify(),
            ":target_methodology" => $this->gettarget_methodology(),
            ":target_formula" => $this->gettarget_formula(),
            ":target_unit" => $this->gettarget_unit(),
            ":target_baseline" => $this->gettarget_baseline(),
            ":target_value" => $this->gettarget_value(),
            ":target_dtstart" => $this->gettarget_dtstart(),
            ":target_dtfinish" => $this->gettarget_dtfinish(),
            ":target_frequence" => $this->gettarget_frequence(),
            ":target_frequenceunit" => $this->gettarget_frequenceunit()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_targets a WHERE a.target_id=:id", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_targets SET 
                obj_id=:obj_id,
                user_id=:user_id,
                target_name=:target_name,
                target_code=:target_code,
                target_description=:target_description,
                target_justify=:target_justify,
                target_methodology=:target_methodology,
                target_formula=:target_formula,
                target_unit=:target_unit,
                target_baseline=:target_baseline,
                target_value=:target_value,
                target_dtstart=:target_dtstart,
                target_dtfinish=:target_dtfinish,
                target_frequence=:target_frequence,
                target_frequenceunit=:target_frequenceunit
            WHERE target_id=:target_id", array(
                ":obj_id" => $this->getobj_id(),
                ":user_id" => $this->getuser_id(),
                ":target_name" => $this->gettarget_name(),
                ":target_code" => $this->gettarget_code(),
                ":target_description" => $this->gettarget_description(),
                ":target_justify" => $this->gettarget_justify(),
                ":target_methodology" => $this->gettarget_methodology(),
                ":target_formula" => $this->gettarget_formula(),
                ":target_unit" => $this->gettarget_unit(),
                ":target_baseline" => $this->gettarget_baseline(),
                ":target_value" => $this->gettarget_value(),
                ":target_dtstart" => $this->gettarget_dtstart(),
                ":target_dtfinish" => $this->gettarget_dtfinish(),
                ":target_frequence" => $this->gettarget_frequence(),
                ":target_frequenceunit" => $this->gettarget_frequenceunit(),
                ":target_id" => $this->gettarget_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_targets WHERE target_id=:id", array(
            ":id" => $this->gettarget_id()
        ));
    }
    
    public static function getFrequenceUnitsList(){
        $list = array(
            1 => "Dia",
            2 => "Semana",
            3 => "Mês",
            4 => "Ano"
        );
        return $list;
    }
            
}
?>