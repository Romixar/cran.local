<?php

class Router{
    
    public $routes = [
        '/rules' => 'main/rules',
        '/news' => 'news/index',
        '/statistic' => 'statistic/index',
        '/faq' => 'main/faq',
        '/works' => 'works/index',
        '/reklams' => 'main/reklams',
        '/contacts' => 'main/contacts',
        '/refpage' => 'main/refpage',
        '/login' => 'main/login',
        '/profile' => 'main/profile',
        '/logout' => 'main/logout',
        '/registration' => 'main/registration',
    ];
    
    public $get; // псевдо GET параметры буду передавать
    
    public function getURL(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);// без GET
    }
    
    public function getCtrlAndAction(&$ctrl, &$act){
        
        $url = $this->getURL();
        
        $arr = explode('/',$url);
        
        // если есть GET параметр, то назначить $this->get
        if(isset($arr[2])){
            
            if($arr[1] == 'registration') $this->get = $arr[2];
            if($arr[1] == 'news') $this->get = $arr[2];
            if($arr[1] == 'profile') $this->get = $arr[2];
            
            unset($arr[2]);
        }
        
        
        
        
        $ctrl = '';
        $act = '';
        
        if(count($arr) == 2 && !empty($arr[1])){
            
            foreach($this->routes as $k => $v){
                
                $arrk = explode('/',$k);
                
                if($arrk[1] == $arr[1]){
                    
                    $arrv = explode('/',$v);
                    $ctrl = $arrv[0];
                    $act = $arrv[1];
                }
                
            }
            if($ctrl !== '' && $act !== ''){
                $ctrl = ucfirst($ctrl).'Controller';
                $act = 'action'.ucfirst($act);
                return;
            }
            
        }
        
        // в случае если не найдено контроллеров, запуск главной стьраницы
        $ctrl = 'Main';
        $act = 'Index';
            
        
        
        $ctrl = $ctrl.'Controller';
        $act = 'action'.$act;
        
    }
    
    public function getGET($k=''){
        if($k) return $_GET[$k];
        else return $_GET;
    }
    
    
    
    
    public function start(){
        session_start();
        $this->getCtrlAndAction($ctrl, $act);
        $c = new $ctrl();
        $c->$act($this->get);
        
        
        echo 'CONTROLLER - '.$ctrl.'<br/>ACTION - '.$act;
        
    }
    
    
    
    
}


?>