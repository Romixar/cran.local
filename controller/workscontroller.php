<?php

class WorksController extends Controller{
    
    public $nmsWorks = ['Серфинг','Задания','Тесты','Конкурсы','новый таб'];
    
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';

        
        $data = $this->getSerfing();
        
        debug($data);
        
        $content = $this->view->prerender('serf');
        
        $names = $this->getTabs('class="active"','names');
        $tabs = $this->getTabs(' in active','tabs',$content);

        $this->render('works',compact('names','tabs'));
    }
    
    
    public function getTabs($class,$tpl,$content=''){
        
        $arr = $this->nmsWorks;
        
        for($i=0; $i<count($arr); $i++){
            $cl = ($i == 0) ? $class : '';
            
            $content = ($i == 0) ? $content : '';
            
            $name = $arr[$i];
            
            $str .= $this->view->prerender($tpl,compact('i','cl','name','content'));
        }
        return $str;
    }
    
    public function getSerfing(){
        
        $mod = new Serfing();
        
        return $mod->find('*');
        
        
        
    }
    
    
    
    
    
}




?>