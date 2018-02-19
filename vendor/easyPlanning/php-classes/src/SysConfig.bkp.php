<?php
namespace easyPlanning;

class SysConfig{
    //CAMINHOS
    const IMG_URL = self::SITE_URL . "";
    
    //INFORMÇÕES BÁSICAS DO SITE
    const SITE_URL = "";
    const SITE_PASTA = "";
    const SITE_NOME = "";
    const SITE_EMAIL_ADM = "";
    
    //INFORMAÇÕES DO BD
    const BD_HOST = '';
    const BD_USER = '';
    const BD_PASS = '';
    const BD_BANCO = '';
    const BD_PREFIX = '';
    
    //INFORMAÇÕES PARA O PHP MAILER
    const MAIL_HOST = "";
    const MAIL_NAME = "";
    const MAIL_USER = "";
    const MAIL_PASS = "";
    const MAIL_PORT = ;
    const MAIL_SMTP_AUTH = ;
    const MAIL_SMTP_SECURE = "";
    const MAIL_CHARSET = "";
    const MAIL_IS_HTML = ;
    const MAIL_COPIA = "";
        
    //INFORMAÇÕES PARA CRIPTOGRAFIA
    //Create Two Random Keys And Save Them In Configuration File
    //echo base64_encode(openssl_random_pseudo_bytes(32));
    //echo base64_encode(openssl_random_pseudo_bytes(64));
}

?>
 