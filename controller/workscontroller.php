<?php

class WorksController extends Controller{
    
    public $nmsWorks = ['Серфинг','Задания','Тесты','Конкурсы','новый таб'];
    
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';

        if(isset($_SESSION['user'])){
            
            $data = $this->getSerfing();
            
            $content = $this->getHtmlSerf($data);
            
        }else $content = $this->getEmptyContent();// если пользователь не авторизован
        
        
        
        //debug($data);die;

        
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
        
        $yes_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $tod_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $user_id = $_SESSION['user']['id'];
        
        $fields = '`serfing`.`id`,`n`,`v`,`timer`,`url`,`title`,`desc`,`price`,`period`,
        `history_s`.`serf_ids`,`history_s`.`dates_views`';
        
        // сначала пробую извлечь из serfing и history_s
        $data = $mod->findSerfLinks($fields, $user_id, $yes_ts, $tod_ts);
        
        
        if(empty($data)) return $mod->find('*');// если нет, значит сегодня ещё не серфил
        else return $data;
    }
    
    public function getHtmlSerf($data){
        
        $fl = 0;// чтобы узнать были ли полные итерации
        
        $str = '<div class="panel-group serf" id="collapse-group">';
        
        for($i=0; $i<count($data); $i++){
            
            $ost = $data[$i]->n - $data[$i]->v; // если уже были просмотренные у юзера
            
            $cl = ''; // будет класс для неактивных серфинг ссылок
            
            if($ost == 0 || ($data[$i]->serf_ids && !$this->checkSerfLink($data[$i]->id, $data[$i]))){
                $cl = ' disabled';
                continue; // не будет выводиться просмотренные ссылки
            }

            $str .= $this->view->prerender('serf',[
                'i'    => $i,
                'id'   => $data[$i]->id,
                'n'    => $data[$i]->n,
                'ost'  => $ost,
                'timer'=> $data[$i]->timer,
                'url'  => $data[$i]->url,
                'title'=> $data[$i]->title,
                'price'=> $data[$i]->price,
                'desc' => $data[$i]->desc,
                'cl'   => $cl,
                'rand' => rand(1,4),
            ]);
            
            $fl = 1;
        }
        
        $str .= ($fl) ? '</div>' : $this->getEmptyContent($fl).'</div>';

        return $str;
        
    }
    
    public function getEmptyContent($fl=1){
        
        $str1 = '<p>Для авторизованных пользователей в этом разделе доступен заработок</p>';
        
        $str2 = '<div class="noserf">Ссылок для серфинга пока нет. Проверьте позже.</div>';
        
        return ($fl) ? $str1 : $str2;
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
            else $price = ($data[0]->n == $data[0]->v) ? 0 : $data[0]->price;
        
        }else $price = 0;
        
        
        // отчечаю просмотр в таблице serfing
        if($this->acceptView($serf_id) != 2) $this->getAlertJS('Ошибка обновления просмотра ссылки!');
        
        if(empty($data)) $this->insertSerfLink($serf_id, $user_id, $ts, $price);
            
        // проверка на нажатие уже просмотренных ссылок
        if(!$this->checkSerfLink($serf_id, $data[0])) $this->getAlertJS('Ссылка уже просмотрена!');
        
        $this->updateSerfLink($data, $serf_id, $user_id, $ts, $price);
        
        
        
        
    }
    
    public function acceptView($serf_id){
        
        $mod = new Serfing();
        
        return $mod->updateViewSerf($serf_id);
        
    }
    
    public function updateBalances($price){// обновить баланс пользователя и реферера если есть
        
        $balance = $_SESSION['user']['balance'];
        
        /// % отчисления реферерру, если он есть
        $this->updateRefBalances($this->getRefTax($price),$price,false);
        
        $balance = round(($_SESSION['user']['balance'] - $balance), 4);
        
        
        $this->replFrameContent('На ваш баланс зачислено '.$balance.' руб.!');
        
        
    }
    
    public function getSerfDataOnDay($serf_id, $user_id){
        
        $mod = new History_s();
        
        // период текущие сутки
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $today_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $f = '`user_id`,`serf_ids`,`dates_views`,`date_add`,`sum`,
        `serfing`.`price`,`serfing`.`period`,`serfing`.`n`,`serfing`.`v`';
        
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
            
        }else $this->getAlertJS('Ошибка обновления в БД просмотренных ссылок!');
    }

    
    public function checkSerfLink($serf_id, $data){// проверка времени просмотра ссылки
        
        $serf_ids = substr($data->serf_ids,0,-1);
        
        $arr = explode(',',$serf_ids);
        
        if(in_array($serf_id, $arr)){
            
            $dates_views = substr($data->dates_views,0,-1);
            
            $arr_dates = explode(',',$dates_views);
            
            foreach($arr as $k => $v){
                
                if($v == $serf_id) $ts_view = $arr_dates[$k];// TS когда была просмотрена ссылка
            }
            
            if(($ts_view + $data->period) > time()) return false; // нельзя просматривать
            else return true;
        }
        return true;
        
    }
    
    public function getSerfPrice($serf_id){
        
        $serf = new Serfing();
        
        $data = $serf->find('`id`,`price`,`n`,`v`','`id`='.$serf_id);
        
        $price = ($data[0]->n == $data[0]->v) ? 0 : $data[0]->price;
        
        return $price;
        
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