<?php

class Router{
    
    public $route = [];
    
    public function getURL(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);// без GET
    }
    
    public function getCtrlAndAction(&$ctrl, &$act){
        
        $url = $this->getURL();
        
        $arr = explode('/',$url);
        
        if(count($arr) == 2){
            $ctrl = '';
            $act = 'Index';
        }else{
            $ctrl = ucfirst($arr[1]);
            $act = ucfirst($arr[2]);
        }
        
        $ctrl = $ctrl.'Controller';
        $act = 'action'.$act;
        
    }
    
    public function getGET($k=''){
        if($k) return $_GET[$k];
        else return $_GET;
    }
    
    
    
    
    public function start(){
        
        $this->getCtrlAndAction($ctrl, $act);
        $c = new $ctrl();
        $c->$act();
        
        
        echo 'CONTROLLER - '.$ctrl.'<br/>ACTION - '.$act;
        
    }
    
    
    
    
}


?>