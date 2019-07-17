<?php
namespace easyPlanning;

class SysConfig_dev{
    //INFORMÇÕES BÁSICAS DO SITE
    const SITE_URL = "http://www.easy.com.br";
    //const SITE_URL = "http://www.treinarecife.com.br";
    const SITE_PASTA = "";
    const SITE_NOME = "Treina Recife - Easy Planning";
    const SITE_EMAIL_ADM = "titokenzo@treinarecife.com.br";
    
    //CAMINHOS
    const RESOURCES_URL = self::SITE_URL . "/res/";
    const IMG_URL = self::RESOURCES_URL . "imgs/";
    const CSS_URL = self::RESOURCES_URL . "css/";
    const JS_URL = self::RESOURCES_URL . "js/";
    const VENDORS_URL = self::SITE_URL . "/res/vendors/";
    const VENDORS_CSS_URL = self::VENDORS_URL . "css/";
    const VENDORS_JS_URL = self::VENDORS_URL . "js/";
    
    //INFORMAÇÕES DO BD
    const BD_HOST = 'localhost:3306';
    //const BD_HOST = 'easyplanning.mysql.uhserver.com:3306';
    const BD_USER = 'easyplanning_us';
    const BD_PASS = '3@syP@55f0rMy5q1';
    const BD_BANCO = 'easyplanning';
    const BD_PREFIX = '';
    
    //INFORMAÇÕES PARA O PHP MAILER
    const MAIL_HOST = "smtp.treinarecife.com.br";
    const MAIL_NAME = "Treina Recife - Easy Planning";
    const MAIL_USER = "easyplanning@treinarecife.com.br";
    const MAIL_PASS = "Tr31n@R3c1f3";
    const MAIL_PORT = 587;
    const MAIL_SMTP_AUTH = TRUE;
    const MAIL_SMTP_SECURE = "";
    const MAIL_CHARSET = "utf8_decode()";
    const MAIL_IS_HTML = TRUE;
    const MAIL_COPIA = "titokenzo@treinarecife.com.br";

    //const MAIL_NAME = "Treina Recife - Easy Planning";
    
    //INFORMAÇÕES PARA CRIPTOGRAFIA
    //Create Two Random Keys And Save Them In Configuration File
    //echo base64_encode(openssl_random_pseudo_bytes(32));
    //echo base64_encode(openssl_random_pseudo_bytes(64));
    const FIRSTKEY = 'QaqVQZRtD9X6+R13DCdDTCLkdIWQ5p6+yLnmnTzB5k0=';
    const SECONDKEY = 'G9y2uqgLc9DllyOewt45PZLlxWbsOEKA0cAWl/q8Tnw+vbDQ9L/QaZapHxINZW8HvcPGrQQlSrKkXTJKxaAdhg==';
    
    public static function getSiteCfg(){
        $site = array(
            "url"=>self::SITE_URL,
            "nome"=>self::SITE_NOME,
            "admin"=>self::SITE_EMAIL_ADM,
            "js"=>self::JS_URL,
            "imgs"=>self::IMG_URL,
            "css"=>self::CSS_URL,
            "libcss"=>self::VENDORS_CSS_URL,
            "libjs"=>self::VENDORS_JS_URL
        );
        return $site;
    }
}

?>
 