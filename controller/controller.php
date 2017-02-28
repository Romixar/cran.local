<?php

class Controller{
    
    public $view;// объект видов
    
    public function __construct(){
        $this->view = new ViewController();
        //if(!isset($_SESSION['user'])) LoginController::Auth();
        if(!isset($_SESSION['user'])){
            $this->view->render('login');
            exit();
        }
        
    }
    
    
    public function actionIndex(){
        
        
        echo 'работает';
        
    }
    
    
    
    
    
    
    
    
    
    
    
}








?>