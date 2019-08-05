<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Monitoring extends Model
{

    public function __construct()
    {
        $fields = array(
            "target_id",
            "mon_id",
            "mon_dtcreation",
            "mon_comment",
            "mon_date",
            "mon_image"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_monitoring;");
    }

    public static function listObjectiveResults($idobj)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_monitoring a INNER JOIN tb_targets b USING (target_id) WHERE b.obj_id=:OBJ ORDER BY a.target_name;", array(":OBJ"=>$idobj));
    }
    
    public static function listPlanLastResults($idplan)
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
                DATEDIFF(a.target_dtfinish,a.target_dtstart) as dias_total,
                DATEDIFF(e.mon_date,a.target_dtstart) as dias_leitura,
                b.obj_id,
                b.obj_description,
                c.plan_id,
                c.plan_title,
                d.persp_id,
                d.persp_name,
                e.mon_value
            FROM tb_monitoring e 
            INNER JOIN (SELECT Max(mon_date) as max_data,target_id as target FROM tb_monitoring GROUP BY target_id) f ON e.target_id=f.target AND e.mon_date=f.max_data 
            RIGHT JOIN tb_targets a USING(target_id) 
            INNER JOIN tb_objectives b USING (obj_id) 
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE c.plan_id=:ID ORDER BY d.persp_name, b.obj_description, a.target_name;", array(":ID"=>$idplan));
        if($results){
            $results = self::proccessResults($results);
        }
        return $results;
    }
    
    public static function listObjectiveLastResults($idobj)
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
                DATEDIFF(a.target_dtfinish,a.target_dtstart) as dias_total,
                DATEDIFF(e.mon_date,a.target_dtstart) as dias_leitura,
                b.obj_id,
                b.obj_description,
                c.plan_id,
                c.plan_title,
                d.persp_id,
                d.persp_name,
                e.mon_id,
                e.mon_value,
                e.mon_date
            FROM tb_monitoring e
            INNER JOIN (SELECT Max(mon_date) as max_data,target_id as target FROM tb_monitoring GROUP BY target_id) f ON e.target_id=f.target AND e.mon_date=f.max_data
            RIGHT JOIN tb_targets a USING(target_id)
            INNER JOIN tb_objectives b USING (obj_id)
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE a.obj_id=:ID ORDER BY d.persp_name, b.obj_description, a.target_name;", array(":ID"=>$idobj));
        if($results){
            $results = self::proccessResults($results);
        }
        return $results;
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
                DATEDIFF(a.target_dtfinish,a.target_dtstart) as dias_total,
                DATEDIFF(e.mon_date,a.target_dtstart) as dias_leitura,
                b.obj_id,
                b.obj_description,
                c.plan_id,
                c.plan_title,
                d.persp_id,
                d.persp_name,
                e.mon_id,
                e.mon_value,
                e.mon_date,
                e.mon_comment
            FROM tb_monitoring e
            INNER JOIN tb_targets a USING (target_id)
            INNER JOIN tb_objectives b USING (obj_id)
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE a.target_id=:ID ORDER BY e.mon_date DESC;", array(":ID"=>$idtarget));
        if($results){
            $results = self::proccessResults($results);
        }
        return $results;
    }
    
    public static function getTargetMetaData($idtarget)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT
                a.target_id,
                a.target_name,
                a.target_value,
                a.target_baseline,
                a.target_dtstart,
                a.target_code,
                a.target_unit,
                a.target_dtfinish,
                a.target_frequence,
                a.target_frequenceunit,
                a.target_description,
                a.target_justify,
                a.target_formula,
                a.target_methodology,
                a.user_id,
                b.obj_id,
                b.obj_description,
                c.plan_id,
                c.plan_title,
                d.persp_id,
                d.persp_name
            FROM tb_targets a
            INNER JOIN tb_objectives b USING (obj_id)
            INNER JOIN tb_strategic_planning c USING (plan_id)
            INNER JOIN tb_perspectives d USING(persp_id)
            WHERE a.target_id=:ID;", array(":ID"=>$idtarget));
        return $results[0];
    }
    
    private static function proccessResults($results=array()){
        $max = count($results);
        for ($i=0; $i<$max;$i++){
            $valor = 0;
            $meta = 1;
            $results[$i]["resultado"] = 0;
            $results[$i]["resultado_relativo"] = 0;
            $results[$i]["nivel"] = 0;
            $results[$i]["nivel_relativo"] = 0;
            if($results[$i]["dias_inicio"] >= 0){
                if(isset($results[$i]["mon_value"])){
                    $meta = abs($results[$i]["target_value"] - $results[$i]["target_baseline"]);
                    $meta = ($meta==0)?1:$meta;
                    $valor = $results[$i]["mon_value"] - $results[$i]["target_baseline"];
                    if($results[$i]["target_value"] > $results[$i]["target_baseline"]){
                        $valor = max($valor,0);
                    }else{
                        $valor = abs(min($valor,0));
                    }
                }
                $dias = max($results[$i]["dias_leitura"],1);
                $meta_relativa = $dias * $meta / $results[$i]["dias_total"];
                $res = round( min( ($valor*100/$meta) , 100) , 2);
                $res_relativo = round( min( ($valor*100/$meta_relativa) , 100) , 2);
                $results[$i]["resultado_relativo"] = $res_relativo;
                $results[$i]["resultado"] = $res;
                if($res>80){
                    $results[$i]["nivel"] = 1;
                }elseif($res>40){
                    $results[$i]["nivel"] = 2;
                }else{
                    $results[$i]["nivel"] = 3;
                }
                
                if($res_relativo>80){
                    $results[$i]["nivel_relativo"] = 1;
                }elseif($res_relativo>40){
                    $results[$i]["nivel_relativo"] = 2;
                }else{
                    $results[$i]["nivel_relativo"] = 3;
                }
            }
        }
        return $results;
    }
    
    public static function proccessObjectivesResults($res=array()) {
        $lista = array();
        $max = count($res);
        $i = 0;
        $idx = 0;
        while($i<$max){
            $obj = $res[$i]["obj_id"];
            $lista[$idx]["obj_id"] = $obj;
            $lista[$idx]["obj_description"] = $res[$i]["obj_description"];
            $total = 0;
            $total_relativo = 0;
            $ctd = 0;
            $ctd_relativo = 0;
            while($i<$max AND $res[$i]["obj_id"]==$obj){
                if($res[$i]["nivel"]>0){
                    $total += $res[$i]["resultado"];
                    $total_relativo += $res[$i]["resultado_relativo"];
                    $ctd++;
                    $ctd_relativo++;
                }
                $i++;
            }
            $media = $ctd==0?0:round( $total/$ctd , 2);
            $media_relativa = $ctd_relativo==0?0:round( $total_relativo/$ctd_relativo , 2);
            $lista[$idx]["valor"] = $total;
            $lista[$idx]["resultado"] = $media;
            $lista[$idx]["resultado_relativo"] = $media_relativa;
            if($media>80){
                $lista[$idx]["nivel"] = 1;
            }elseif($media>40){
                $lista[$idx]["nivel"] = 2;
            }else{
                $lista[$idx]["nivel"] = 3;
            }
            
            if($media_relativa>80){
                $lista[$idx]["nivel_relativo"] = 1;
            }elseif($media_relativa>40){
                $lista[$idx]["nivel_relativo"] = 2;
            }else{
                $lista[$idx]["nivel_relativo"] = 3;
            }
            $idx++;
        }
        return $lista;
    }
    
    public function save(){
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_monitoring (
            target_id,
            mon_value,
            mon_comment,
            mon_date
        ) VALUES(
            :target_id,
            :mon_value,
            :mon_comment,
            :mon_date
        );", array(
            ":target_id" => $this->gettarget_id(),
            ":mon_value" => $this->getmon_value(),
            ":mon_comment" => $this->getmon_comment(),
            ":mon_date" => $this->getmon_date()
        ));
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_monitoring a WHERE a.mon_id=:id", array(
            ":id" => $id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_monitoring SET 
                target_id=:target_id,
                mon_value=:mon_value,
                mon_comment=:mon_comment,
                mon_date=:mon_date
            WHERE mon_id=:mon_id;", array(
                ":target_id" => $this->gettarget_id(),
                ":mon_value" => $this->getmon_value(),
                ":mon_comment" => $this->getmon_comment(),
                ":mon_date" => $this->getmon_date(),
                ":mon_id" => $this->getmon_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_monitoring WHERE mon_id=:id", array(
            ":id" => $this->getmon_id()
        ));
    }
    
}
