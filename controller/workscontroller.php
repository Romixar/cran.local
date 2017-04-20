<?php

class WorksController extends Controller{
    
    public $nmsWorks = ['Серфинг','Задания','Тесты','Конкурсы','новый таб'];
    
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';

        
        $data = $this->getSerfing();
        
        //debug($data);

        
        $content = $this->getHtmlSerf($data);

        
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
    
    public function getHtmlSerf($data){
        
        $str = '<div class="panel-group serf" id="collapse-group">';
        
        for($i=0; $i<count($data); $i++){
            
            
            $str .= $this->view->prerender('serf',[
                'i'=>$i,
                'id'=>$data[$i]->id,
                'n'=>$data[$i]->n,
                'timer'=>$data[$i]->timer,
                'url'=>$data[$i]->url,
                'title'=>$data[$i]->title,
                'price'=>$data[$i]->price
            ]);
            
            
        }
        
        $str .= '</div>';
        return $str;
        
    }
    
    
    public function addSerfView(){
        
        
        
        $mod = new History_s();
        
        // период текущие сутки
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $today_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $data = $mod->find('`user_id`, `serf_ids`, `date_add`','`user_id` = '.$_SESSION['user']['id'].' AND `date_add` BETWEEN '.$yesterday_ts.' AND '.$today_ts);
        
        if(empty($data)){
            
            // записать ID просмотренной ссылки в строку serf_ids
            
            $id = (int) $this->data['serf_id'];
            
            if(is_int($id) && preg_match('/^\d{1,10}$/',$id)){
            
                $serf_ids = $id.',';
            
            
                $res = $mod->insert([
                   'user_id' => $_SESSION['user']['id'],

                   'serf_ids' => $serf_ids,

                   'date_add' => time(),
                ]);
                
                if($res) exit($res);
                
                die;
                
                //else $this->respJson($this->sysMessage('danger','Ошибка добавления нового реферала!'));
                
            }

            
        }else{
            
            if(count($data) != 1) exit; // ошибка, т.к. в сутки только по одной строке на юзера
            
            // добавляю к уже просмотренную юзером ссылкам, еще одну
            
            $id = (int) $this->data['serf_id'];
            
            $serf_ids = $data[0]->serf_ids.$id.',';
            
            $res = $mod->update([
                
                'serf_ids' => $serf_ids,

            ],'`user_id` = '.$_SESSION['user']['id']);
            
            
            
            if($res) exit($res);
                
            die;
            
            
        }
        
        
        
        
        debug($data);die;
        
        

        
        
        
        
        
        
        debug($this->data);die;
        
        
        
    }
    

    
    
    
    
    
}




?>