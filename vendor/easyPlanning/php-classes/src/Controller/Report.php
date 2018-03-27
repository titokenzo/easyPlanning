<?php
namespace easyPlanning\Controller;

use easyPlanning\DB\Sql;

class Report
{

    public function __construct()
    {}

    public static function getOrganizationLevelList()
    {
        $list = array(
            0 => "Estratégico",
            1 => "Tático",
            2 => "Operacional"
        );
        return $list;
    }

    public function listAllQuestions($quest_environment = 0)
    {
        $sql = new Sql();
        return $sql->select("SELECT a.quest_id, a.persp_id, a.qset_id, a.quest_text, b.qset_name, c.persp_name   
            FROM tb_questions a
            INNER JOIN tb_questions_sets b USING(qset_id)
            INNER JOIN tb_perspectives c USING(persp_id)
            WHERE a.quest_environment=:ENV
            ORDER BY a.quest_text;", array(
            ":ENV" => $quest_environment
        ));
    }

    public function listDistinctPerspectives($quest_environment = 0)
    {
        $sql = new Sql();
        return $sql->select("SELECT DISTINCT c.persp_name,c.persp_id 
            FROM tb_questions a
            INNER JOIN tb_perspectives c USING(persp_id)
            WHERE a.quest_environment=:ENV
            ORDER BY c.persp_name;", array(
            ":ENV" => $quest_environment
        ));
    }

    public function listGrade1Swot($diagnostic_id=0)
    {
        $sql = new Sql();
        return $sql->select("SELECT AVG(d.res_grade) as media, a.quest_environment, a.quest_negativeswot, a.quest_positiveswot, b.qset_name, c.persp_name
            FROM tb_questions a
            INNER JOIN tb_questions_sets b USING(qset_id)
            INNER JOIN tb_perspectives c USING(persp_id)
            INNER JOIN tb_responses d USING(quest_id)
            INNER JOIN tb_respondents e USING(resp_id)
            WHERE e.diagnostic_id=:ID AND d.res_grade>0
            GROUP BY a.quest_environment, a.quest_negativeswot, a.quest_positiveswot, b.qset_name, c.persp_name 
            ORDER BY c.persp_name;", array(
                ":ID" => $diagnostic_id
            ));
    }
    
    public function listGrade2Swot($diagnostic_id=0)
    {
        $sql = new Sql();
        return $sql->select("SELECT AVG(d.res_grade2) as media, a.quest_environment, a.quest_negativeswot, a.quest_positiveswot, b.qset_name, c.persp_name
            FROM tb_questions a
            INNER JOIN tb_questions_sets b USING(qset_id)
            INNER JOIN tb_perspectives c USING(persp_id)
            INNER JOIN tb_responses d USING(quest_id)
            INNER JOIN tb_respondents e USING(resp_id)
            WHERE e.diagnostic_id=:ID AND d.res_grade2>0
            GROUP BY a.quest_environment, a.quest_negativeswot, a.quest_positiveswot, b.qset_name, c.persp_name
            ORDER BY c.persp_name;", array(
                ":ID" => $diagnostic_id
            ));
    }
    
    /**
     * Retorna uma lista com todas as entredas do Respondente
     * 
     * @return array
     */
    public function listInputs($diagnostic_id=0)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_responses_inputs a INNER JOIN tb_respondents b USING(resp_id) WHERE b.diagnostic_id =:ID;", array(
            ":ID" => (int) $diagnostic_id
        ));
    }
}
?>