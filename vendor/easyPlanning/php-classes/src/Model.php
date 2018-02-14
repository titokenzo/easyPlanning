<?php 
namespace easyPlanning;

class Model{
    private $values = [];
    
    public function __call($name, $args){
        $method = substr($name, 0, 3);
        $attribute = substr($name, 3, strlen($name));
        
        switch($method){
            case "get":
                return $this->value[$attribute];
                break;
            case "set":
                $this->values[$attribute] = $args[0];
                break;
        }
    }
    
    public function setData($data = array()){
        foreach ($data as $key => $value){
            $this->{"set".$key}($value);
        }
    }
    
    public function getValues(){
        return $this->values;
    }
}
?>