<?php

class WorksController extends Controller{
    
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';
        
        
        
        $this->render('works');
    }
    
    
    
    
    
}




?>