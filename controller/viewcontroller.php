<?php

class ViewController{
    
    //public $vars = [];
    
    
    
    public function render($tmpl,$data=[]){
        
        //$content = $this->prerender($tmpl,$data);
        
//debug($data);
        
        $this->display($tmpl,$data);
    }
    
    
    public function prerender($tmpl,$data=[]){
        
        //echo Config::$adm;
        
        $file = 'view/'.$tmpl.'_tpl.php';
        
        if(file_exists($file)){
            
            if($data) extract($data);
            ob_start();
            include $file;
            return ob_get_clean();
        }
        return false;
    }
    
    public function display($tmpl,$data=[]){
        echo $this->prerender($tmpl,$data);
    }
    
    
    
    
}


?>