<?php
namespace easyPlanning\Model;

use easyPlanning\DB\Sql;
use easyPlanning\Model;

class Organization extends Model
{

    public function __construct()
    {
        $fields = array(
            "org_id",
            "org_companyname",
            "org_tradingname",
            "org_cnpj",
            "org_size",
            "org_legalnature",
            "org_dtcreation",
            "org_logo",
            "org_addrbilling",
            "org_addrbilling_complement",
            "org_addrbilling_city",
            "org_addrbilling_state",
            "org_addrbilling_zip",
            "org_addrbilling_country",
            "org_address",
            "org_addr_complement",
            "org_addr_city",
            "org_addr_state",
            "org_addr_zip",
            "org_addr_country",
            "org_contact_name",
            "org_contact_email",
            "org_contact_phone",
            "org_notification"
        );
        $this->setAttrs($fields);
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * from tb_organizations o ORDER BY o.org_companyname");
    }

    public function save()
    {
        $sql = new Sql();
        
        $results = $sql->query("INSERT INTO tb_organizations (
            org_companyname,
            org_tradingname,
            org_cnpj,
            org_size,
            legalnature_id,
            org_logo,
            org_addrbilling,
            org_addrbilling_complement,
            org_addrbilling_city,
            org_addrbilling_state,
            org_addrbilling_zip,
            org_addrbilling_country,
            org_address,
            org_addr_complement,
            org_addr_city,
            org_addr_state,
            org_addr_zip,
            org_addr_country,
            org_contact_name,
            org_contact_email,
            org_contact_phone,
            org_notification,
            org_status
        ) VALUES(
            :companyname,
            :tradingname,
            :cnpj,
            :size,
            :legalnature,
            :logo,
            :addrbilling,
            :addrbilling_complement,
            :addrbilling_city,
            :addrbilling_state,
            :addrbilling_zip,
            :addrbilling_country,
            :address,
            :addr_complement,
            :addr_city,
            :addr_state,
            :addr_zip,
            :addr_country,
            :contact_name,
            :contact_email,
            :contact_phone,
            :notification,
            :status
        )", array(
            ":companyname" => $this->getorg_companyname(),
            ":tradingname" => $this->getorg_tradingname(),
            ":cnpj" => $this->getorg_cnpj(),
            ":size" => $this->getorg_size(),
            ":legalnature" => $this->getlegalnature_id(),
            ":logo" => $this->getorg_logo(),
            ":addrbilling" => $this->getorg_addrbilling(),
            ":addrbilling_complement" => $this->getorg_addrbilling_complement(),
            ":addrbilling_city" => $this->getorg_addrbilling_city(),
            ":addrbilling_state" => $this->getorg_addrbilling_state(),
            ":addrbilling_zip" => $this->getorg_addrbilling_zip(),
            ":addrbilling_country" => $this->getorg_addrbilling_country(),
            ":address" => $this->getorg_address(),
            ":addr_complement" => $this->getorg_addr_complement(),
            ":addr_city" => $this->getorg_addr_city(),
            ":addr_state" => $this->getorg_addr_state(),
            ":addr_zip" => $this->getorg_addr_zip(),
            ":addr_country" => $this->getorg_addr_country(),
            ":contact_name" => $this->getorg_contact_name(),
            ":contact_email" => $this->getorg_contact_email(),
            ":contact_phone" => $this->getorg_contact_phone(),
            ":notification" => $this->getorg_notification(),
            ":status" => $this->getorg_status()
        ));
        
        // $this->setData($results[0]);
    }

    public function get($orgid)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_organizations o WHERE o.org_id=:id", array(
            ":id" => $orgid
        ));
        
        $this->setData($results[0]);
    }

    public function update()
    {
        $sql = new Sql();
        
        $sql->query("UPDATE tb_organizations SET 
                org_companyname=:companyname,
                org_tradingname=:tradingname,
                org_cnpj=:cnpj,
                org_size=:size,
                legalnature_id=:legalnature,
                org_logo=:logo,
                org_addrbilling=:addrbilling,
                org_addrbilling_complement=:addrbilling_complement,
                org_addrbilling_city=:addrbilling_city,
                org_addrbilling_state=:addrbilling_state,
                org_addrbilling_zip=:addrbilling_zip,
                org_addrbilling_country=:addrbilling_country,
                org_address=:address,
                org_addr_complement=:addr_complement,
                org_addr_city=:addr_city,
                org_addr_state=:addr_state,
                org_addr_zip=:addr_zip,
                org_addr_country=:addr_country,
                org_contact_name=:contact_name,
                org_contact_email=:contact_email,
                org_contact_phone=:contact_phone,
                org_notification=:notification,
                org_status=:status
            WHERE org_id=:id", array(
            ":companyname" => $this->getorg_companyname(),
            ":tradingname" => $this->getorg_tradingname(),
            ":cnpj" => $this->getorg_cnpj(),
            ":size" => $this->getorg_size(),
            ":legalnature" => $this->getlegalnature_id(),
            ":logo" => $this->getorg_logo(),
            ":addrbilling" => $this->getorg_addrbilling(),
            ":addrbilling_complement" => $this->getorg_addrbilling_complement(),
            ":addrbilling_city" => $this->getorg_addrbilling_city(),
            ":addrbilling_state" => $this->getorg_addrbilling_state(),
            ":addrbilling_zip" => $this->getorg_addrbilling_zip(),
            ":addrbilling_country" => $this->getorg_addrbilling_country(),
            ":address" => $this->getorg_address(),
            ":addr_complement" => $this->getorg_addr_complement(),
            ":addr_city" => $this->getorg_addr_city(),
            ":addr_state" => $this->getorg_addr_state(),
            ":addr_zip" => $this->getorg_addr_zip(),
            ":addr_country" => $this->getorg_addr_country(),
            ":contact_name" => $this->getorg_contact_name(),
            ":contact_email" => $this->getorg_contact_email(),
            ":contact_phone" => $this->getorg_contact_phone(),
            ":notification" => $this->getorg_notification(),
            ":status" => $this->getorg_status(),
            ":id" => $this->getorg_id()
        ));
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_organizations WHERE org_id=:id", array(
            ":id" => $this->getorg_id()
        ));
    }

    public static function getLegalNatureList()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_legalnatures ORDER BY legalnature_name");
    }

    public static function getSizeList()
    {
        $lista = array(
            1 => "Micro",
            2 => "Pequena",
            3 => "Média",
            4 => "Grande"
        );
        return $lista;
    }

    public static function getStatusList()
    {
        $lista = array(
            0 => "Inativo",
            1 => "Ativo",
            2 => "Concluído",
            3 => "Pendente",
            4 => "Suspenso"
        );
        return $lista;
    }
}
?>