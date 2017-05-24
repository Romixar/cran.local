<?php

class WorksController extends Controller{
    
    
    public function actionIndex(){
        $this->title = 'Страница Задания / работы';
        $this->meta_desc = 'Страница Задания / работы мета описание';
        $this->meta_key = 'Страница Задания / работы мета кей';

        if(isset($_SESSION['user'])){
            
            $content = $this->getHtmlSerf($this->getSerfing()); //  серфинг ссылки
            
            //$staticlinks = $this->getHtmlStaticLinks($this->getContextLinks());// статические ссылки
            $links = $this->getHtmlStaticLinks($this->getContextLinks());// статические ссылки
            
            $section = 'Статические ссылки (не оплачиваемые)';
            
            //$content .= $this->view->prerender('staticlinks',compact('staticlinks'));
            $content .= $this->view->prerender('sectionlinks',compact('section','links'));
            
            $links = '';
            
            
        }else $content = $this->getEmptyContent();// если пользователь не авторизован
            
        
        
        
        $names = $this->getTabs('class="active"','names');// названия вкладок
        $tabs = $this->getTabs(' in active','tabs',$content);// содержимое первой активной вкладки
        
        
        $section = 'Текстовые объявления';
        $links = $this->getHtmlTxtLinks($this->getTextLinks());// текст ссылки
        
        $txtlinks = $this->view->prerender('sectionlinks',compact('section','links'));
        
        

        $this->render('works',compact('names','tabs','txtlinks'));
    }
    
    public function getTextLinks(){
        
        $mod = new Textlinks();
        
        return $mod->find('`id`,`desc`,`url`,`h`,`period`,`date_add`','','`date_add` DESC');
        
    }
    
    public function getContextLinks(){
        
        $mod = new Contextlinks();
        
        return $mod->find('*');
        
    }

    public function getSerfing(){
        
        $mod = new Serfing();
        
        $yes_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $tod_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $mon_ts = time() - (31 * 24 * 60 * 60);
        
        $user_id = $_SESSION['user']['id'];
        
        $fields = '`serfing`.`id`,`serfing`.`user_id`,`opt`,`n`,`v`,`tot_v`,`k`,`timer`,`h`,`url`,
                  `title`,`desc`,`price`,`period`,`serfing`.`date_add`,`history_s`.`serf_ids`,
                  `history_s`.`dates_views`';
        
        
        
        // сначала пробую извлечь из serfing и history_s за послед сутки
        $data = $mod->findSerfLinks($fields, $user_id, $yes_ts, $tod_ts, $mon_ts);
        
        
        $f = '`id`,`user_id`,`opt`,`n`,`v`,`tot_v`,`k`,`timer`,`h`,`url`,
             `title`,`desc`,`price`,`period`,`date_add`';
        
        $w = '`date_add` > '.(time() - 31 * 24 * 60 * 60);// за послед месяц
        
        if(empty($data)) $data = $mod->find($f,$w,'`date_add` DESC');// значит юзер сег-ня ещё не серфил
        
        $data = $this->addZeroSerfingView($data);// обнуление просмотров v, если начались нов сутки
        
        
        return $data;
    }
    
    public function addZeroSerfingView($data){// присвоить 0 v у которых прошли сутки
        
        $yes_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        
        $tmp = [];// ключ (ID ссылки) => знач-е (k кол-во уже прошедших суток)
        
        $j=0;
        for($i=0; $i<count($data); $i++){// если у периода серф ссылки прошли сутки, то обнул V
            
            if(($data[$i]->date_add + 24 * 60 * 60) > time()) continue;// не прошли сутки с подачи
            
            if(($data[$i]->date_add + $data[$i]->period) < time()) continue;// давно истек период
            
            
            // найти TS полночи того дня когда была добавлена ссылка
            $polnoch = mktime(0,0,0,date('m',$data[$i]->date_add),date('d',$data[$i]->date_add),date('Y',$data[$i]->date_add));
                
            // из TS сегодняшней полночи вычесть полночь date_add и разделить на сек в сут.
            $days = ($yes_ts - $polnoch) / (24 * 60 * 60);// кол-во полных суток до сегодняш полночи
            
            $perioddays = $data[$i]->period / (24 * 60 * 60);// кол-во пол сут периода ссылки
            

            
            if($days > $perioddays) continue;// ограничение на окончание
                
            if($data[$i]->k != $days){// Обнулить v у этой ссылки и установить k и tot_v
                
                $tmp[$j]['id'] = $data[$i]->id;
                $tmp[$j]['days'] = $days;
                $tmp[$j]['tot_v'] = $data[$i]->v + $data[$i]->tot_v;// просмотры за прош сутки
                
                $data[$i]->k = $days;// кол-во полных пройденных суток
                
                $data[$i]->v = 0;
                
                $j++;
            }
            
        }

        
        if(!empty($tmp) && !$this->udateSerfViewsOnDay($tmp))// обнуляю v и обновляю k в БД
            $this->sysMessage2('danger','Ошибка обновления серфинг ссылки!');
        
        return $data;
        
    }
    
    public function udateSerfViewsOnDay($tmp){
        
        $mod = new Serfing();
        
        if($mod->setSerfZero($tmp)) return true;
        return false;
    }
    
    
    public function getHtmlSerf($data){
        
        $fl = 0;// чтобы узнать были ли полные итерации
        
        $arrlogins = $this->getLoginsOnIDs($data);
        
        
        $str = '<div class="panel-group serf" id="collapse-group">';
        
        for($i=0; $i<count($data); $i++){
            
            //if(($data[$i]->date_add + $data[$i]->period) < time()) continue;// давно истек период
            if((time() - $data[$i]->date_add) > $data[$i]->period) continue;// давно истек период
            
            $ost = $data[$i]->n - $data[$i]->v; // если уже были просмотренные у юзера
            
            if($ost <= 0 || ($data[$i]->serf_ids && !$this->checkSerfLink($data[$i]->id, $data[$i]))){
                $cl = ' disabled';
                continue; // не будет выводиться просмотренные ссылки
            }
            
            $url = ($data[$i]->h) ? 'https://'.$data[$i]->url : 'http://'.$data[$i]->url;
            
            $cl = ($data[$i]->opt) ? '' : ' red';//будет класс для неактивных серфинг ссылок и выдел-е

            $str .= $this->view->prerender('serf_link',[
                'i'    => $i,
                'id'   => $data[$i]->id,
                'v'    => $data[$i]->v,
                'tot_v'=> $data[$i]->tot_v,
                'ost'  => $ost,
                'timer'=> $data[$i]->timer,
                'url'  => $url,
                'login'=> $arrlogins[$data[$i]->user_id],
                'date_add'=>$this->formDate($data[$i]->date_add),
                'date_fin'=>$this->formDate($data[$i]->date_add + $data[$i]->period),
                'title'=> $data[$i]->title,
                'price'=> $data[$i]->price,
                'desc' => $data[$i]->desc,
                'cl'   => $cl,
                'rand' => rand(1,4), //будет случайная кнопка с заработком
            ]);
            
            
            
            $fl = 1;
        }
        
        $str .= ($fl) ? '</div>' : $this->getEmptyContent($fl).'</div>';

        return $str;
        
    }
    
    public function getLoginsOnIDs($data){
        
        $ids = [];// ID рекламодателей
        
        for($i=0; $i<count($data); $i++) $ids[] = $data[$i]->user_id;// собираю ID рекламодателей
        
        $mod = new User();
        $res = $mod->findLoginOnIds($ids);

        for($j=0; $j<count($res); $j++){
            foreach($res[$j] as $k => $v) if($k == 'id') $tmp[$v] = $res[$j]['login'];
        }
        return $tmp;
    }
    
    public function getHtmlStaticLinks($data){

        for($i=0; $i<count($data); $i++){
            
            if(($data[$i]->date_add + $data[$i]->period) <= time()) continue;
            
            $str .= $this->view->prerender('st_link',[
                
                'i'    => $i,
                'id'   => $data[$i]->id,
                'v'    => $data[$i]->v,
                'url'  => $data[$i]->url,
                'title'=> $data[$i]->title,
                
            ]);
        }
        return $str;
    }
    
    public function getHtmlTxtLinks($data){
        
        for($i=0; $i<count($data); $i++){
            
            if(($data[$i]->date_add + $data[$i]->period) <= time()) continue;
            
            $h = ($data[$i]->h) ? 'https://' : 'http://';
            
            $str .= $this->view->prerender('txt_link',[
                
                'i'    => $i,
                'id'   => $data[$i]->id,
                'url'  => $h.$data[$i]->url,
                'desc' => $data[$i]->desc,
                
            ]);
        }
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
        
        //debug($this->data);die;
        
        $data = $this->getSerfDataOnDay($serf_id, $user_id);
        

        // ошибка, т.к. в сутки только по одной строке на юзера
        if(!empty($data) && count($data) != 1) $this->getAlertJS('Ошибка БД!');
        
        
        if($this->data['view']){// просмотр прерван или нет
                
            if(empty($data)) $price = $this->getSerfPrice($serf_id);
            else $price = ($data[0]->n == $data[0]->v) ? 0 : $data[0]->price;
        
        }else $price = 0;
        
        
        // отмечаю просмотр в таблице serfing
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
    
    public function getSerfDataOnDay($serf_id, $user_id){// данные о серф ссылке за сутки
        
        $mod = new History_s();
        
        // период текущие сутки
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $today_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи сегодн дня
        
        $f = '`history_s`.`user_id`,`serf_ids`,`dates_views`,`history_s`.`date_add`,`sum`,
        `serfing`.`price`,`serfing`.`period`,`serfing`.`n`,`serfing`.`v`';
        
        return $mod->findSerfData($f, $serf_id, $user_id, $yesterday_ts, $today_ts);
        
    }
    
    public function insertSerfLink($serf_id, $user_id, $ts, $price){// просмотр серф ссылки
        
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
    
    
    public function addViewStaticLink(){// зафиксировать просмотр статич ссылки
        
        $link_id = $this->data['linkId'];
        $user_id = $_SESSION['user']['id'];
        
        $ts = time();
        
        if(!is_int($link_id) && !preg_match('/^\d{1,10}$/',$link_id))
            $this->respJson($this->sysMessage('danger','Ошибка ID статической ссылки!'));
        
        // вставка либо обновление истории просмотра (неделя на строку)
        $data = $this->getStaticLinkOnDay($link_id, $user_id);
        
        // ошибка, т.к. в сутки (неделю) только по одной строке на юзера   обновление просмотра v
        if((!empty($data) && count($data) != 1) || ($this->acceptLinkView($link_id) != 2))
            $this->respJson($this->sysMessage('danger','Ошибка извлечения из БД!'));
        

        if(empty($data)) $res = $this->insertStaticLink($link_id, $user_id, $ts);
        else $res = $this->updateStaticLink($data, $link_id, $user_id, $ts);
        
        if($res) exit('Зафиксирован неоплачиваемый просмотр!');
        else $this->respJson($this->sysMessage('danger','Ошибка добавления просмотра ссылки!'));
    }
    
    public function acceptLinkView($link_id){
        
        $mod = new Contextlinks();
        
        return $mod->updateViewSerf($link_id);
    }
    
    public function insertStaticLink($link_id, $user_id, $ts){
        $mod = new History_st();
        return $mod->insert([
                   'user_id' => $user_id,
                   'view_ids' => $link_id.',',
                   'dates_views' => $ts.',',
                   'date_add' => $ts,
               ]);
    }
    
    public function updateStaticLink($data, $link_id, $user_id, $ts){
        $mod = new History_st();
        
        // добавляю к уже просмотренным юзером ссылкам, еще одну
        $view_ids = $data[0]->view_ids.$link_id.',';
            
        return $mod->update([
                   'view_ids' => $view_ids,
                   'dates_views' => $data[0]->dates_views.$ts.',',
                   'date_add' => $ts,

               ],'`user_id` = '.$user_id.' AND `date_add` = '.$data[0]->date_add);
    }
    
    public function getStaticLinkOnDay($link_id, $user_id){
        
        $mod = new History_st();
        
        // период текущая неделя
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $week_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи ч\з неделю
        
        $f = '`history_st`.`user_id`,`view_ids`,`dates_views`,`history_st`.`date_add`,
             `contextlinks`.`period`,`contextlinks`.`n`,`contextlinks`.`v`';
        
        return $mod->findStaticLinkData($f, $link_id, $user_id, $yesterday_ts, $week_ts);
        
    }
    public function getCntxtLinkOnDay($link_id, $user_id){
        
        $mod = new History_c();
        
        // период текущая неделя
        $yesterday_ts = mktime(0,0,0,date('m'),date('d'),date('Y'));// TS полночи этого дня
        $week_ts = mktime(0,0,0,date('m'),(date('d')+1),date('Y'));// TS полночи ч\з неделю
        
        $f = '`history_c`.`user_id`,`view_ids`,`dates_views`,`history_c`.`date_add`,
             `contextlinks`.`period`,`contextlinks`.`n`,`contextlinks`.`v`';
        
        return $mod->findCntxtLinkData($f, $link_id, $user_id, $yesterday_ts, $week_ts);
    }
    
    
    public function addViewCntxtLink(){

        $link_id = $this->data['linkId'];
        $user_id = $_SESSION['user']['id'];
        
        $ts = time();
        
        if(!is_int($link_id) && !preg_match('/^\d{1,10}$/',$link_id))
            $this->respJson($this->sysMessage('danger','Ошибка ID контекстной ссылки!'));
        
        // вставка либо обновление истории просмотра (неделя на строку)
        $data = $this->getCntxtLinkOnDay($link_id, $user_id);
        
        // ошибка, т.к. в сутки (неделю) только по одной строке на юзера   обновление просмотра v
        if((!empty($data) && count($data) != 1) || ($this->acceptLinkView($link_id) != 2))
            $this->respJson($this->sysMessage('danger','Ошибка извлечения из БД!'));
        

        if(empty($data)) $res = $this->insertCntxtLink($link_id, $user_id, $ts);
        else $res = $this->updateCntxtLink($data, $link_id, $user_id, $ts);
        
        if($res) exit('Зафиксирован неоплачиваемый просмотр!');
        else $this->respJson($this->sysMessage('danger','Ошибка добавления просмотра ссылки!'));
    }
    
    public function insertCntxtLink($link_id, $user_id, $ts){
        $mod = new History_c();
        return $mod->insert([
                   'user_id' => $user_id,
                   'view_ids' => $link_id.',',
                   'dates_views' => $ts.',',
                   'date_add' => $ts,
               ]);
    }
    
    public function updateCntxtLink($data, $link_id, $user_id, $ts){
        $mod = new History_c();
        
        // добавляю к уже просмотренным юзером ссылкам, еще одну
        $view_ids = $data[0]->view_ids.$link_id.',';
            
        return $mod->update([
                   'view_ids' => $view_ids,
                   'dates_views' => $data[0]->dates_views.$ts.',',
                   'date_add' => $ts,

               ],'`user_id` = '.$user_id.' AND `date_add` = '.$data[0]->date_add);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
}




?>