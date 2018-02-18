<?php
namespace easyPlanning;

use Rain\Tpl;

class Mailer extends SysConfig{
    
    private $mail;
    
    public function __construct($toAddr, $toName, $subject, $tplName, $data=array()){

        $config = array(
            "tpl_dir"       => $_SERVER['DOCUMENT_ROOT']."/view/email/",
            "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/view-cache/",
            "debug"         => false
        );
        
        Tpl::configure( $config );
        
        $tpl = new Tpl;
        
        foreach ($data as $key => $value){
            $tpl->assign($key, $value);
        }
        
        $html = $tpl->draw($tplName, true);
        
        $this->mail = new \PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPDebug=0;
        $this->mail->Debugoutput='html';
        $this->mail->Host = self::MAIL_HOST;// 'smtp.gmail.com';
        $this->mail->Port = self::MAIL_PORT;
        $this->mail->SMTPSecure = self::MAIL_SMTP_SECURE;// 'tls';
        $this->mail->SMTPAuth = self::MAIL_SMTP_AUTH;
        $this->mail->Username = self::MAIL_USER;
        $this->mail->Password = self::MAIL_PASS;
        $this->mail->setFrom(self::MAIL_USER, self::MAIL_NAME);
        $this->mail->addAddress($toAddr, $toName);
        $this->mail->Subject = $subject;
        $this->mail->msgHTML($html);
        $this->mail->AltBody = 'Treina Recife';
    }
    
    public function send(){
        if(!$this->mail->send()){
            throw new \Exception('Erro ao enviar formulário: ' . $this->mail->ErrorInfo);
        } 
        //return $this->mail->send();
    }
}
?>