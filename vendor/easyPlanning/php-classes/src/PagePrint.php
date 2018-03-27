<?php
namespace easyPlanning;

use Rain\Tpl;
use easyPlanning\Model\User;

class PagePrint{
	private $tpl;
	private $options = [];
	private $defaults = [
        "header"=>true,
	    "footer"=>true,
	    "data"=>[]
	];
	
	public function __construct($opts = array()){
		$this->options = array_merge($this->defaults,$opts);
		
		$config = array(
			"tpl_dir"       => $_SERVER['DOCUMENT_ROOT']."/view/",
			"cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/view-cache/",
			"debug"         => false
		);

		Tpl::configure( $config );
		$this->tpl = new Tpl;
		
		$this->options["data"]["cfg"] = SysConfig::getSiteCfg();
		
        if(isset($_SESSION[User::SESSION])){
            $this->options["data"]["logged"] = $_SESSION[User::SESSION];
        }
		$this->setData($this->options["data"]);
        
        if($this->options["header"]===true)
		  $this->tpl->draw("header-print");
	}
	
	public function setTpl($name, $data = array(), $returnHTML = false){
	    $this->setData($data);
	    return $this->tpl->draw($name,$returnHTML);
	}
	
	private function setData($data = array()){
	    foreach($data as $key => $value){
	        $this->tpl->assign($key, $value);
	    }
	}
	
	public function __destruct(){
	    if($this->options["footer"]===true)
		  $this->tpl->draw("footer-print");
	}
}
?>