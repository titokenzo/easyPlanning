<?php
namespace easyPlanning;

use Rain\Tpl;

class Mailer{
    
    const USERNAME = "titolixao@gmail.com";
    const PASSWORD = "kju#6463";
    const NAME_FROM = "Treina Recife - Easy Planning";
    
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
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = 587;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->SMTPAuth = TRUE;
        $this->mail->Username = Mailer::USERNAME;
        $this->mail->Password = Mailer::PASSWORD;
        $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);
        $this->mail->addAddress($toAddr, $toName);
        $this->mail->Subject = $subject;
        $this->mail->msgHTML($html);
        $this->mail->AltBody = '';
    }
    
    public function send(){
        return $this->mail->send();
    }
}
?>