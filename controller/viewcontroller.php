<?php

class ViewController{
    
    
    public function render($tmpl,$data=[]){
        
        $content = $this->prerender($tmpl,$data);
        
        $this->display('main',compact('content'));
    }
    
    
    public function prerender($tmpl,$data=[]){
        
        $file = 'D:\OpenServer\domains\cran.local\view\\'.$tmpl.'_tpl.php';
        
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