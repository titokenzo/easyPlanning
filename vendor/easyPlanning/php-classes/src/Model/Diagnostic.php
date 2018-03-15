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

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_objectives (
            plan_id,
            persp_id,
            user_id,
            obj_description,
            obj_positionx,
            obj_positiony,
            obj_sizex,
            obj_sizey
        ) VALUES(
            :plan_id,
            :persp_id,
            :user_id,
            :obj_description,
            :obj_positionx,
            :obj_positiony,
            :obj_sizex,
            :obj_sizey
        )", array(
            ":plan_id" => $this->getplan_id(),
            ":persp_id" => $this->getpersp_id(),
            ":user_id" => $this->getuser_id(),
            ":obj_description" => $this->getobj_description(),
            ":obj_positionx" => $this->getobj_positionx(),
            ":obj_positiony" => $this->getobj_positiony(),
            ":obj_sizex" => $this->getobj_sizex(),
            ":obj_sizey" => $this->getobj_sizey()
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
        
        $sql->query("UPDATE tb_strategic_planning SET 
                plan_id=:plan_id,
                persp_id=:persp_id,
                user_id=:user_id,
                obj_description=:obj_description,
                obj_positionx=:obj_positionx,
                obj_positiony=:obj_positiony,
                obj_sizex=:obj_sizex,
                obj_sizey=:obj_sizey 
            WHERE obj_id=:obj_id", array(
            ":plan_id" => $this->getplan_id(),
            ":persp_id" => $this->getpersp_id(),
            ":user_id" => $this->getuser_id(),
            ":obj_description" => $this->getobj_description(),
            ":obj_positionx" => $this->getobj_positionx(),
            ":obj_positiony" => $this->getobj_positiony(),
            ":obj_sizex" => $this->getobj_sizex(),
            ":obj_sizey" => $this->getobj_sizey(),
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