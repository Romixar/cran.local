<?php

class WorksController extends Controller{
    
    public $nmsWorks = ['Серфинг','Задания','Тесты','Конкурсы','новый таб'];
    
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';

        
        
        
        $names = $this->getTabs('class="active"','names');
        $tabs = $this->getTabs(' in active','tabs');

        $this->render('works',compact('names','tabs'));
    }
    
    
    public function getTabs($class,$tpl){
        
        $arr = $this->nmsWorks;
        
        for($i=0; $i<count($arr); $i++){
            $cl = ($i == 0) ? $class : '';
            $name = $arr[$i];
            
            $str .= $this->view->prerender($tpl,compact('i','cl','name'));
        }
        return $str;
    }
    
    
    
    
    
}




?>