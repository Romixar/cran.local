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
    
    
    public function addSerfView(){// добавление просмотра серфинга
        
        $user_id = $_SESSION['user']['id'];
        
        $ts = time();// дата просмотра
        
        $serf_id = (int) $this->data['serf_id'];
        
        if(!is_int($serf_id) && !preg_match('/^\d{1,10}$/',$serf_id)) $this->getAlertJS('Ошибка ID!');
        
        $data = $this->getSerfDataOnDay($serf_id, $user_id);
        

        // ошибка, т.к. в сутки только по одной строке на юзера
        if(!empty($data) && count($data) != 1) $this->getAlertJS('Ошибка БД!');
        
        
        if($this->data['view']){// просмотр прерван или нет
                
            if(empty($data)) $price = $this->getSerfPrice($serf_id);
            else $price = $data[0]->price;
        }else $price = 0;
        
        
        
        if(empty($data)) $this->insertSerfLink($serf_id, $user_id, $ts, $price);
            
        // проверка на нажатие уже просмотренных ссылок
        if(!$this->checkSerfLink($serf_id, $data)) $this->getAlertJS('Ошибка! Ссылка уже просмотрена.');
        
        $this->updateSerfLink($data, $serf_id, $user_id, $ts, $price);
        
        
        
        
    }
    
    public function updateBalances($price){// обновить баланс пользователя и реферера если есть
        
        $balance = $_SESSION['user']['balance'];
        
        /// % отчисления реферерру, если он есть
        $this->updateRefBalances($this->getRefTax($price),$price,false);
        
        $balance = round(($_SESSION['user']['balance'] - $balance), 4);
        
        
        $this->replFrameContent('На ваш баланс зачислено '.$balance.' руб.!');
        
        //$this->getAlertJS('На ваш баланс зачислено '.$balance.' руб.!');
        
    }
    
    public function getSerfDataOnDay($serf_id, $user_id){
        
        $mod = new History_s();
        
        // период текущие сутки
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $today_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $f = '`user_id`,`serf_ids`,`dates_views`,`date_add`,`sum`,`serfing`.`price`,`serfing`.`period`';
        
        return $mod->findSerfData($f, $serf_id, $user_id, $yesterday_ts, $today_ts);
        
    }
    
    public function insertSerfLink($serf_id, $user_id, $ts, $price){
        
        $mod = new History_s();
            
        $res_id = $mod->insert([
                
           'user_id' => $user_id,

           'serf_ids' => $serf_id.',',// записать ID просмотренной ссылки в строку serf_ids
                
           'dates_views' => $ts.',',

           'date_add' => $ts,
            
           'sum' => $price
        ]);
                
        if($res_id){
            
            $this->updateBalances($price);
            
            //return true;
            
            //debug($res_id);exit();
        
        }else $this->getAlertJS('Ошибка добавления в БД просмотренной ссылки!');
    }
    
    public function updateSerfLink($data, $serf_id, $user_id, $ts, $price){
        
        $mod = new History_s();
        
        // добавляю к уже просмотренным юзером ссылкам, еще одну
        $serf_ids = $data[0]->serf_ids.$serf_id.',';
            
        $res = $mod->update([
                
            'serf_ids' => $serf_ids,
            
            'dates_views' => $data[0]->dates_views.$ts.',',
                
            'date_add' => $ts,
                
            'sum' => ($data[0]->sum + $price),

        ],'`user_id` = '.$user_id.' AND `date_add` = '.$data[0]->date_add);
        
        if($res){
            
            $this->updateBalances($price);
            
            //return true;
            
            //debug($res);exit();
            
        }else $this->getAlertJS('Ошибка обновления в БД просмотренных ссылок!');
    }
    
    
    
    public function checkSerfLink($serf_id, $data){
        
        $serf_ids = substr($data[0]->serf_ids,0,-1);
        
        $arr = explode(',',$serf_ids);
        
        if(in_array($serf_id, $arr)){
            
            $dates_views = substr($data[0]->dates_views,0,-1);
            
            $arr_dates = explode(',',$dates_views);
            
            foreach($arr as $k => $v){
                
                if($v == $serf_id) $ts_view = $arr_dates[$k];// TS когда была просмотрена ссылка
            }
            
            if(($ts_view + $data[0]->period) > time()) return false; // нельзя просматривать
            else return true;
        }
        return true;
        
    }
    
    public function getSerfPrice($serf_id){
        
        $serf = new Serfing();
        
        $data = $serf->find('`id`,`price`','`id`='.$serf_id);
        
        return $data[0]->price;
        
    }
    
    public function replFrameContent($mes){
        
        echo json_encode([
            'replFrCont'=>$mes,
        ]);
        exit();
        
    }
    
    
    public function getAlertJS($sysmes){
        
        echo json_encode([
            'alert'=>$sysmes,
        ]);
        exit();
    }

    
    
    
    
    
}




?>