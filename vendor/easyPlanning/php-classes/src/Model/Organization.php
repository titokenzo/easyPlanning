<?php 
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Organization extends Model{
    const SESSION = "User";
    
    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * from tb_organizations o ORDER BY o.org_companyname");
    }
    
    public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_organizations_save(:name, :login, :pass, :email, :phone, :isadmin)", array(
            ":name"=> $this->getperson_name(),
            ":login"=> $this->getuser_login(),
            ":pass"=> $this->getuser_password(),
            ":email"=> $this->getperson_email(),
            ":phone"=> $this->getperson_phone(),
            ":isadmin"=> $this->getuser_isadmin()
        ));
        
        //$this->setData($results[0]);
    }
    
    public function get($orgid){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_organizations o WHERE o.org_id=:id", array(
            ":id"=>$orgid
        ));
        
        $this->setData($results[0]);
    }
    
    public function update(){
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_usersupdate_save(:id, :name, :login, :pass, :email, :phone, :isadmin)", array(
            ":id"=> $this->getuser_id(),
            ":name"=> $this->getperson_name(),
            ":login"=> $this->getuser_login(),
            ":pass"=> $this->getuser_password(),
            ":email"=> $this->getperson_email(),
            ":phone"=> $this->getperson_phone(),
            ":isadmin"=> $this->getuser_isadmin()
        ));
        
        //$this->setData($results[0]);
    }
    
    public function delete(){
        $sql=new Sql();
        $sql->query("CALL sp_users_delete(:id)", array(
            ":id"=>$this->getuser_id()
        ));
    }
    
    public static function getLegalNatureList(){
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_legalnature ORDER BY legalnature_name");
    }
    
    
}
?>