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
        
        $ts = time();// дата просмотра
        
        $serf_id = (int) $this->data['serf_id'];
        
        if(!is_int($serf_id) && !preg_match('/^\d{1,10}$/',$serf_id)) $this->getAlertJS('Ошибка ID!');
        
        $mod = new History_s();
        
        // период текущие сутки
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $today_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $user_id = $_SESSION['user']['id'];
        
        $f = '`user_id`,`serf_ids`,`dates_views`,`date_add`,`sum`,`serfing`.`price`,`serfing`.`period`';
        
        $data = $mod->findSerfData($f, $serf_id, $user_id, $yesterday_ts, $today_ts);
        
        // ошибка, т.к. в сутки только по одной строке на юзера
        if(!empty($data) && count($data) != 1) $this->getAlertJS('Ошибка БД!');
        
        
        if(!empty($data)){
            
            // проверка нажатия уже просмотренных ссылок
            
            if(!$this->checkSerfLink($serf_id, $data[0]->serf_ids, $data[0]->dates_views, $data[0]->period)) $this->getAlertJS('Ошибка! Ссылка уже просмотрена');
            
            
        }
        
        
        
        if(empty($data)){
            
            $price = $this->getSerfPrice($serf_id);
            
            // записать ID просмотренной ссылки в строку serf_ids
            $serf_ids = $serf_id.',';
            
            $res_id = $mod->insert([
                
               'user_id' => $user_id,

               'serf_ids' => $serf_ids,
                
               'dates_views' => $ts.',',

               'date_add' => $ts,
                
               'sum' => $price
            ]);
                
            if($res_id){debug($res_id);die;}
            else $this->getAlertJS('Ошибка добавления в БД просмотренной ссылки!');
        }
            
        // добавляю к уже просмотренным юзером ссылкам, еще одну
        $serf_ids = $data[0]->serf_ids.$serf_id.',';
            
        $res = $mod->update([
                
            'serf_ids' => $serf_ids,
            
            'dates_views' => $data[0]->dates_views.$ts.',',
                
            'date_add' => $ts,
                
            'sum' => ($data[0]->sum + $data[0]->price),

        ],'`user_id` = '.$user_id.' AND `date_add` = '.$data[0]->date_add);
            
        
        if($res){debug($res);die;}
        else $this->getAlertJS('Ошибка обновления в БД просмотренных ссылок!');
    }
    
    public function checkSerfLink($serf_id, $serf_ids, $dates_views, $period){
        
        $serf_ids = substr($serf_ids,0,-1);
        
        $arr = implode(',',$serf_ids);
        
        if(in_array($serf_id, $arr)){
            
            
            
            
        }
        return false;
        
        
        
    }
    
    public function getSerfPrice($serf_id){
        
        $serf = new Serfing();
        
        $data = $serf->find('`id`,`price`','`id`='.$serf_id);
        
        return $data[0]->price;
        
    }
    
    
    public function getAlertJS($sysmes){
        
        echo json_encode([
            'alert'=>$sysmes,
        ]);
        exit();
    }

    
    
    
    
    
}




?>