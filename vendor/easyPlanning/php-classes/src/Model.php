<?php 
namespace easyPlanning;

class Model{
    private $values = [];
    private $attrs = [];
    
    public function setData($data = array()){
        foreach ($data as $key => $value){
            //$value = html_entity_decode($value);
            $this->{"set".$key}($value);
        }
    }
    
    public function getValues(){
        return $this->values;
    }
    
    public function setAttrs($arg = array()){
        $this->attrs = $arg;
    }
    
    /**
     * Retorna o vetor com a lista de atributos da classe
     * @return Array
     */
    public function getAttrs(){
        return $this->attrs;
    }
    
    public function __call($name, $args){
        $method = substr($name, 0, 3);
        $attribute = substr($name, 3, strlen($name));
        
        switch($method){
            case "get":
                return $this->values[$attribute];
            case "set":
                $this->values[$attribute] = $args[0];
                break;
        }
    }
}
?>