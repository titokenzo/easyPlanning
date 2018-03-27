<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;
use easyPlanning\Mailer;
use easyPlanning\Security;
use easyPlanning\SysConfig;

class Respondent extends Model
{

    public function __construct()
    {
        $fields = array(
            "resp_id",
            "org_id",
            "resp_email",
            "resp_dtcreation",
            "resp_allowreturn",
            "resp_allowpartial",
            "resp_hasemailerror",
            "resp_hascompleted",
            "resp_orglevel",
            "resp_dtentered",
            "resp_dtfinished",
            "resp_lastip"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_respondents a INNER JOIN tb_organizations b USING(org_id) ORDER BY b.org_tradingname, a.respondent_email");
    }
    
    public function saveRespodents(){
        $emails = explode(",", $this->getresp_email());
        foreach ($emails as $value){
            $this->setresp_email(trim($value));
            $this->save();
        }
    }

    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_respondents a WHERE a.resp_email=:resp_email AND a.diagnostic_id=:diagnostic_id", array(
            ":resp_email" => $this->getresp_email(),
            ":diagnostic_id" => (int)$this->getdiagnostic_id()
        ));
        if (count($results) > 0) {
            throw new \Exception("O e-mail " . $this->getresp_email() . " já está cadastrado como respondedor");
        }
        
        $results = $sql->select("CALL sp_respondent_create(:diagnostic_id, :resp_email, :resp_orglevel, :resp_allowreturn, :resp_allowpartial)", array(
            ":diagnostic_id" => $this->getdiagnostic_id(),
            ":resp_email" => $this->getresp_email(),
            ":resp_orglevel" => $this->getresp_orglevel(),
            ":resp_allowreturn" => $this->getresp_allowreturn(),
            ":resp_allowpartial" => $this->getresp_allowpartial()
        ));
        if (count($results) === 0) {
            throw new \Exception("Não foi possível salvar os dados");
        } else {
            $this->setData($results[0]);
            $this->sendDiagnosticEmail();
        }
    }
    
    public function sendDiagnosticEmail(){
        try{
            $code = $this->getresp_id() . '-'. $this->getdiagnostic_id();
            $code = Security::secured_encrypt($code);
            $link = SysConfig::SITE_URL . "/respond?code=$code";
            $mailer = new Mailer($this->getresp_email(), "Colaborador", "EasyPlanning - Questinário", "respondent", array(
                "name" => "Colaborador",
                "link" => $link,
                "org_tradingname" => $_SESSION[User::SESSION]["org_name"]
            ));
            $mailer->send();
        }catch(\Exception $e){
            throw new \Exception("Não foi possível enviar o e-mail.");
        }
    }

    public function get($resp_id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_respondents a WHERE a.resp_id=:id", array(
            ":id" => $resp_id
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        $results = $sql->query("
            UPDATE tb_respondents SET 
                resp_allowreturn=:resp_allowreturn,
                resp_allowpartial=:resp_allowpartial,
                resp_orglevel=:resp_orglevel
            WHERE resp_id=:resp_id", array(
                ":resp_allowreturn" => $this->getresp_allowreturn(),
                ":resp_allowpartial" => $this->getresp_allowpartial(),
                ":resp_orglevel" => $this->getresp_orglevel(),
                ":resp_id" => $this->getresp_id()
            ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_respondents WHERE resp_id=:id", array(
            ":id" => $this->getresp_id()
        ));
    }

    public static function validFormDecrypt($code=NULL)
    {
        if(!isset($code)){
            throw new \Exception("Este link não é válido");
        }
        //$data["resp_id"] . '-'. $data["org_id"]
        $decode = explode("-", Security::secured_decrypt_url($code));
        $resp_id = $decode[0];
        $diag_id = $decode[1];
        $sql = new Sql();
        $results = $sql->select("
            SELECT
                a.resp_id, a.resp_allowreturn, a.resp_allowpartial, c.org_tradingname as org_name 
                FROM tb_respondents a 
                INNER JOIN tb_diagnostics b ON a.diagnostic_id=b.diagnostic_id AND b.diagnostic_status=1
                INNER JOIN tb_organizations c USING(org_id) 
            WHERE 
                a.resp_hascompleted=0 AND 
                a.diagnostic_id=:DIAG AND 
                a.resp_id=:RESP", array(
                    ":DIAG"=>$diag_id,
                    ":RESP" => $resp_id
                ));
        if (count($results) === 0) {
            throw new \Exception("Este link não é mais válido");
        } else {
            return $results[0];
        }
    }

    public static function getOrganizationLevelList()
    {
        $list = array(
            0 => "Estratégico",
            1 => "Tático",
            2 => "Operacional"
        );
        return $list;
    }

    public static function getFromActiveDiagnostic($orgid)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_respondents a INNER JOIN tb_diagnostics b USING(diagnostic_id) WHERE b.org_id=:ID AND b.diagnostic_status IN (1,2) ORDER BY a.resp_email", array(
            ":ID" => (int)$orgid
        ));
    }
    
    /**
     * Salva os dados (AJAX) no BD tb_responses
     * 1. Identifica se o Respondedor já respondeu a questão (Externa).
     * 2. Copia a nota e exclui a ocorrência
     * 3. Ajusta a posição (grade1 ou grade2) da nota salva os dados 
     * @param int $idquest ID da Questão
     * @param int $type Tipo 0=Interna, 1=Externo Ameaça, 2=Externo Oportunidade
     * @param int $grade Nota atribuída
     */
    public function updateResponses($idquest,$type,$grade){
        $grade1 = 0;
        $grade2 = 0;
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM tb_responses WHERE resp_id=:RESP AND quest_id=:QUEST",array(
            ":RESP" => (int)$this->getresp_id(),
            ":QUEST" => (int)$idquest
        ));
        if(count($result) > 0){
            $grade1 = (int)$result[0]["res_grade"];
            $grade2 = (int)$result[0]["res_grade2"];
            $sql->query("DELETE FROM tb_responses WHERE resp_id=:RESP AND quest_id=:QUEST",array(
                ":QUEST" => (int)$idquest,
                ":RESP" => (int)$this->getresp_id()
            ));
        }
        if($type=="2"){
            $grade2 = $grade;
        }else{
            $grade1 = $grade;
        }
        $sql->query("INSERT INTO tb_responses (resp_id,quest_id,res_grade,res_grade2) VALUES (:RESP,:QUEST,:GRADE,:GRADE2)",array(
            ":RESP" => (int)$this->getresp_id(),
            ":QUEST" => (int)$idquest,
            ":GRADE" => $grade1,
            ":GRADE2" => $grade2
        ));
    }

    /**
     * Insere um registro no BD tb_responses_inputs
     * @param int $type Tipo 1-Fraquesa,2-Força,3-Ameaça,4-Oportunidade
     * @param String $input Resposta dada
     */
    public function insertInputs($type,$input){
        $sql = new Sql();
        $result = $sql->select("CALL sp_responses_inputs_save(:RESP,:TEXT,:TYPE)", array(
            ":RESP" => (int)$this->getresp_id(),
            ":TEXT" => $input,
            ":TYPE" => (int)$type
        ));
        if (count($result) === 0) {
            throw new \Exception("Não foi possível salvar os dados");
        }
        return $result[0]["input_id"];
    }
    
    /**
     * Exclui o registro do BD tb_responses_inputs
     * @param int $idinput ID do input
     */
    public function removeInputs($idinput){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_responses_inputs WHERE input_id=:ID",array(
            ":ID" => $idinput
        ));
    }
    
    public function listAllQuestions($quest_environment=0)
    {
        $sql = new Sql();
        return $sql->select("SELECT a.quest_id, a.persp_id, a.qset_id, a.quest_text, b.qset_name, c.persp_name, d.res_grade, d.res_grade2   
            FROM tb_questions a
            INNER JOIN tb_questions_sets b USING(qset_id)
            INNER JOIN tb_perspectives c USING(persp_id)
            LEFT JOIN tb_responses d ON a.quest_id=d.quest_id AND d.resp_id=:RESP 
            WHERE a.quest_environment=:ENV
            ORDER BY a.quest_text;",array(
                ":ENV"=>$quest_environment,
                ":RESP"=>$this->getresp_id()
            ));
    }
    
    public function listDistinctPerspectives($quest_environment)
    {
        $sql = new Sql();
        return $sql->select("SELECT DISTINCT c.persp_name,c.persp_id 
            FROM tb_questions a
            INNER JOIN tb_perspectives c USING(persp_id)
            WHERE a.quest_environment=:ENV
            ORDER BY c.persp_name;", array(
                ":ENV"=>$quest_environment
            ));
    }
    
    /**
     * Retorna uma lista com todas as entredas do Respondente
     * @return array
     */
    public function listInputs()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_responses_inputs WHERE resp_id =:RESP;", array(
                ":RESP"=> (int)$this->getresp_id()
            ));
    }
}
?>