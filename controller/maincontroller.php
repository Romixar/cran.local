<?php

class MainController extends Controller{
    

     
    
    
    public function actionIndex(){
        $this->title = 'Главная страница';
        $this->meta_desc = 'Главная страница мета описание';
        $this->meta_key = 'Главная страница мета кей';


        
        $b_tpl = $this->view->prerender('bonus',compact('bonus'));
        
        $l_tpl = $this->view->prerender('lottery');
        
        
        $this->render('index',compact('b_tpl','l_tpl'));
    }
    
    public function checkLotteryBonus(){
        
        if(!$this->checkResponseBonus()) return;

        if(!is_numeric($this->data['sum'])) $this->respJson($this->sysMessage('danger','Сумма указана не верно!'));
            
        if($this->data['sum'] > $_SESSION['user']['balance']) $this->respJson($this->sysMessage('danger','На вашем счёте не достачно средств!'));
        
        // занести в список играющих и поставить лимит до след игры
        
        $lim = $this->getLimForBonus('lottery');
        
        
        debug($lim);die;
        
        
        $randsund = rand(1, 3);
        
        
        if($this->data['sund_id'] == $randsund){
            
            $bonus = $this->data['sum'] * 2;
            $_SESSION['user']['balance'] += $bonus;
            
            $type = 'success';
            $mes = 'Поздравляем! Сумма в <b>'.$bonus.' руб.</b> зачислена на ваш баланс.';
        }else{
            
            $_SESSION['user']['balance'] -= $this->data['sum'];
            
            $type = 'danger';
            $mes = 'Не угадали! Деньги были в <b>'.$randsund.'</b> сундуке :(((.';
        }
        
        
        
        // занести в БД новый баланс
        
        $u = new User();
        
        $u->update([
            '`balance`'=>$_SESSION['user']['balance']
        ],"`id`=".$_SESSION['user']['id']);
        
        
        // занести в историю лотерей
        
        
        
        
        
        $this->respJson($this->sysMessage($type,$mes));
        
        debug($this->data);die;
        
    }
    
    
    public function actionRules(){
        $this->title = 'Страница правила';
        $this->meta_desc = 'Страница правила мета описание';
        $this->meta_key = 'Страница правила мета кей';
        
        
        $this->render('rules');
    }
    
    public function actionFaq(){
        $this->title = 'Страница FAQ';
        $this->meta_desc = 'Страница FAQ мета описание';
        $this->meta_key = 'Страница FAQ мета кей';
        

        
        $this->render('faq');
    }
    
    public function actionReklams(){
        $this->title = 'Страница рекламодателям';
        $this->meta_desc = 'Страница рекламодателям мета описание';
        $this->meta_key = 'Страница рекламодателям мета кей';
        

        
        
        $this->render('reklams');
    }
    
    public function actionContacts(){
        $this->title = 'Страница контакты';
        $this->meta_desc = 'Страница контакты мета описание';
        $this->meta_key = 'Страница контакты мета кей';
        

        
        
        $this->render('contacts');
    }
    
    public function actionRefstock(){
        $this->title = 'Биржа рефералов';
        $this->meta_desc = 'Страница биржа рефералов мета описание';
        $this->meta_key = 'Страница биржа рефералов мета кей';
        
        //debug($_SESSION['user']);
        
        $u = new User();
        
        $data = $u->getRefStock();// запрос биржи рефералов
        
        //debug($data);//die;
        
        
        
        
        
        $refstock = $this->getHtmlTableStock($data);
        
        
        // мои рефералы в иодальном окне
        $f = '`id`,`t_ref`,`date_ref`,`ref_b`,`login`,`rating`,`date_reg`,`date_act`';
        $data = $u->find($f,"`ref_id`='".$_SESSION['user']['id']."'",'`id` ASC');
        
        $refTable = $this->getHtmlReferals($data, false, true);// мои рефералы с чекбоксами и инпутами
        
        
        //debug($refTable);
        
        
        
        
        $this->render('refstock',compact('refstock','refTable'));
    }
    
    public function addRefStock(){
        
        $refs = json_decode($this->data['referals']);
        
        if(is_array($refs) && !empty($refs)){
            
            for($i=0; $i<count($refs); $i++){
                
                if(count($refs[$i]) != 2) $err = 1;
                
                $id = $_SESSION['user']['id'];
                $lg = $_SESSION['user']['login'];
                
                $values .= '('.$refs[$i][0].','.$id.','.$lg.','.$refs[$i][1].','.time().'),';
                
                $ids[$i] = $refs[$i][0];// ID-эшки выбранных рефералов
            }
            
            if(!$err){
                
                $values = substr($values,0,-1);
            
                $refs = new Refstock();

                $res = $refs->insert([
                    '`user_id`,`seller_id`,`seller`,`price`,`date_add`'=>$values
                ]);
                
                $u = new User();// обновление некот-х данных этих рефералов
                
                $u->updateRefUsers($ids);


                $this->respJson($this->sysMessage('success','Добавлено на биржу '.$res.' рефералов!'));
            }
        }
        
        $this->respJson($this->sysMessage('danger','Ошибка формата данных!'));
    }
    
    public function buyRefStock(){
        
        if(preg_match('/^\d{1,10}$/',$this->data['user_id'])) $id = $this->data['user_id'];
        else $this->respJson($this->sysMessage('danger','Ошибка формата данных!'));

        $mod = new Refstock();
        
        $data = $mod->find('`buy`,`user_id`,`seller_id`,`buyer_id`,`price`','`user_id`='.$id);
        
        if(count($data) !== 1) $this->respJson($this->sysMessage('danger','Ошибка в базе данных!'));
        
        if(($data[0]->user_id == $_SESSION['user']['id']) || ($data[0]->buy == 1)) $this->respJson($this->sysMessage('danger','Покупка невозможна!'));
        
        if($data[0]->price > $_SESSION['user']['balance']) $this->respJson($this->sysMessage('danger','На вашем счете недостаточно средств!'));
        
        
        // обновить в таблице users покупателя, продавца и реферала
        
        $_SESSION['user']['balance'] -= $data[0]->price;
        
           
        $u = new User();
        
        $arrBuyer = [
            'id'=>$_SESSION['user']['id'],// ID покупателя
            'balance'=>$_SESSION['user']['balance'],
            'ref_id'=>0, // без изменений
            'date_ref'=>0,
            't_ref'=>1,  // увеличить на единицу
            'ref_b'=>0,
        ];
        
        $arrRef = [
            'id'=>$data[0]->user_id,// ID реферала
            'balance'=>0,
            'ref_id'=>$_SESSION['user']['id'], 
            'date_ref'=>time(), // дата подключения к рефереру (покупателю)
            't_ref'=>0,
            'ref_b'=>$_SESSION['user']['set_r_b'],
        ];
        
        $arrSeller = [
            'id'=>$data[0]->seller_id,// ID продавца
            'balance'=>$data[0]->price,
            'ref_id'=>0,
            'date_ref'=>0,
            't_ref'=>-1,
            'ref_b'=>0,
        ];
        
        $res = $u->update3Rows([ $arrBuyer, $arrRef, $arrSeller ]);
        
        
        // пометить в таблице биржа рефералов этого реферала как проданный
        
        $upd = $mod->update([
            'buy'=>1,
            'buyer_id'=>$_SESSION['user']['id'],
            'buyer'=>$_SESSION['user']['login'],
            'date_sale'=>time()
        ],'`user_id`='.$data[0]->user_id);
        
        if($upd == 1 && $res == 3) $this->respJson($this->sysMessage('success','Приобретен реферал <b>ID '.$id.' | '.$this->data['login'].'</b>!'), $flname='ok');
        
        $this->respJson($this->sysMessage('danger','Ошибка обновления базы данных!'));
        
        
        // зачислить процент коммиссии на счет системы
        
        
            
        
        
        
        //$lg = $this->getLoginOnID($id);
        
        
        
        debug($data);die;
        die;
        
        
    }
    
    public function actionRefmanage(){
        $this->title = 'Управление рефералами';
        $this->meta_desc = 'Страница управление рефералами мета описание';
        $this->meta_key = 'Страница управление рефералами мета кей';
        
        $u = new User();
        $f = '`id`,`t_ref`,`date_ref`,`ref_b`,`login`,`rating`,`date_reg`,`date_act`';
        $data = $u->find($f,"`ref_id`='".$_SESSION['user']['id']."'",'`id` ASC');
        
        $opt = $this->getHtmlOptions($_SESSION['user']['set_r_b']);
        
        $reflist = $this->getHtmlReferals($data);// рефералы 1-го ур-ня
        

        
        
        $this->render('ref_manage',compact('reflist','opt'));
    }
    
    public function actionRef2lvl(){
        $this->title = 'Управление рефералами';
        $this->meta_desc = 'Страница управление рефералами мета описание';
        $this->meta_key = 'Страница управление рефералами мета кей';
        
        $u = new User();
        $f = '`id`,`ref_id`,`date_ref`,`ref_b`,`login`,`rating`,`date_reg`,`date_act`';
        $w = "`ref_id` IN (SELECT `id` FROM `users` WHERE `ref_id` ='".$_SESSION['user']['id']."')";
        
        $data = $u->find($f,$w);
        
        $reflist = $this->getHtmlReferals($data, 2);// рефералы 2-го ур-ня
        
        
        
        $this->render('ref_2lvl',compact('reflist'));
        
        
    }
    
    public function getHtmlTableStock($data){
        
        $str = '<thead style="font-size:14px">
                    <tr>
                        <th>
                            ID, Логин,<br/>Рейтинг/Продавец
                        </th>
                        <th>
                            Серфинг,<br/>Задания
                        </th>
                        <th>
                            Регистр-я,<br>Присоедин.<br/>Активность
                        </th>
                        <th>
                            Кол-во<br/>рефералов
                        </th>
                        <th>
                            Доход<br/>руб./сут.
                        </th>
                        <th>
                            Цена, руб.
                        </th>
                        <th>
                            -
                        </th>
                    </tr>
                </thead><tbody>';
        
        for($i=0; $i<count($data); $i++){
            
            $str .= '<tr>';
            
            $b = $data[$i]['id'].'<br/>'.$data[$i]['login'].'<br/>'.$data[$i]['rating'].'<br/>'.$data[$i]['seller'];
            
            $c = '34<br/>256';
            
            $d = $data[$i]['date_reg'].'<br/>'.$data[$i]['date_ref'].'<br/>'.$data[$i]['date_act'];
            
            $e = $data[$i]['t_ref'];
            
            $f = '6,3';
            
            $g = $data[$i]['price'];
            
            $h = ($data[$i]['seller_id'] == $_SESSION['user']['id']) ? '<a href="#" id="editref" title="Редактировать"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>' : '<a href="#" id="buy_ref_stock" title="Купить"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a>';
            
            
            $str .= '<td>'.$b.'</td><td>'.$c.'</td><td>'.$d.'</td><td>'.$e.'</td><td>'.$f.'</td><td>'.$g.'</td><td>'.$h.'</td>';
            
            $str .= '</tr>';
            
        }
        
        return $str.'</tbody>';
        
        
    }
    
    public function getHtmlOptions($percent){
        
        for($i=0; $i<10; $i++){
            
            if($i*10 == $percent) $sel = 'selected';
            else $sel = '';
            
            $str .= '<option '.$sel.' value="'.($i/10).'">'.($i*10).'%</option>';    
        }
        return $str;
    }
    
    public function getHtmlReferals($data, $fl='', $chb=''){
        
        $h_r = ($fl) ? '<th>Реферер</th>' : '';
        
        $h_rd = (!$fl) ? '<th>Кол-во<br/>рефералов</th><th>Доход</th>' : '';
        
        $chb = ($chb) ? '<th>-</th>' : '';
        
        $pr = ($chb) ? '<th>Цена</th>' : '';
        
        $str = '<thead><tr>'.$chb.'<th>ID, Логин,<br/>Рейтинг</th>'.$h_r.'
                <th>Серфинг,<br/>Задания</th>
                <th>Регистр-я,<br>Присоедин.<br/>Активность</th>'.$h_rd.'
                <th>Рефбек</th>'.$pr.'</tr></thead><tbody>';
        
        for($i=0; $i<count($data); $i++){
            
            if($fl) $r = '<td>'.$this->getLoginOnID($data[$i]->ref_id).'</td>';
                
            $a = $data[$i]->id.'<br/>'.$data[$i]->login.'<br/>'.$data[$i]->rating;
            
            $b = '56<br/>456';
            
            $c = $data[$i]->date_reg.'<br/>'.$this->formDate($data[$i]->date_ref).'<br/>'.$data[$i]->date_act;
            
            $t_r = (!$fl) ? '<td>'.$data[$i]->t_ref.'</td>' : '';
            
            
            if(!$fl) $e = '<td>'.($this->getRefTaxForRating($data[$i]->rating) * 100).'%</td>'; 
            
            $rfb = $data[$i]->ref_b.'%';
            
            $chBx = ($chb) ? '<td><input type="checkbox" id="'.$data[$i]->id.'" /></td>' : '';
            
            $inp = ($chb) ? '<td><input id="price_'.$data[$i]->id.'" type="text" name="price" /></td>' : '';
            
            
            $str .= '<tr>'.$chBx.'<td>'.$a.'</td>'.$r.'<td>'.$b.'</td><td>'.$c.'</td>'.$t_r.$e.'<td>'.$rfb.'</td>'.$inp.'</tr>';
            
        }
        return $str.'</tbody>';
    }
    
    public function addNewRefBack(){
        
        $u_ids = json_decode($this->data['user_ids']);//  ID моих рефералов
        
        $percent = $this->data['percent_rb'] * 100;
        
        
        if(is_numeric($percent) && ($percent <= 90)){
            
            $u = new User();
            
            $_SESSION['user']['set_r_b'] = $percent;
            
            $res = $u->update([
                'set_r_b'=>$percent
            ],"`id`=".$_SESSION['user']['id']." AND `login`='".$_SESSION['user']['login']."'");
            
            if($res) $this->respJson($this->sysMessage('success','Установлен рефбэк для новых рефералов - <b>'.$percent.'%</b>'));
            
        }
        
        $this->respJson($this->sysMessage('danger','Ошибка установки рефбэка!'));
        
        
        
        //debug($this->data);die;
        
        
        
    }
    
    
    
    
    public function actionRefpage(){
        $this->title = 'Стена рефереров';
        $this->meta_desc = 'Страница стена рефереров мета описание';
        $this->meta_key = 'Страница стена рефереров мета кей';
        
        $user = new User();
        
        $data = $user->getRefPageData();
        
        // удаление последних рефереров, еслт они есть
        $data = $this->delLastRef($data, count($data));

        

        $refpage = $this->getHtmlRefData($data);
        
        //echo json_encode(['dataRefPage'=>$refpage]);// ассинхронный вариант НО НЕБУДЕТ URI
        
        
        
        $this->render('ref_page',compact('refpage'));
    }
    
    public function delLastRef($data){ // удаление последнего реферера со стены рефереров
        
        if(count($data) > 15){
            
            $mod = new Refpage();
            $newdata = array_slice($data,15);// определю эл-ты кот-е удалить
            
            for($i=0; $i<count($newdata); $i++){
                
                $tmp[] = $newdata[$i]['id'];
                
                foreach($data as $k => $v){   
                    if($data[$k]['id'] === $newdata[$i]['id']) unset($data[$k]);
                }   
            }
            $w = '`id` IN ('.implode(',',$tmp).')';
            
            if(!$mod->delete($w)) $this->respJson($this->sysMessage('danger','Ошибка удаления в БД!'));
        }
        return $data;
    }
    
    public function getHtmlRefData($data){
        
        for($i=0; $i<count($data); $i++){
            
            $img = !empty($data[$i]['img']) ? $data[$i]['img'] : 'no-user-image.gif';
                
            $str .= '<div id=ref_'.($i+1).' class="col-sm-4 col-md-4">
            <div class="thumbnail">
              <img src="/images/'.$img.'" alt="проверка" title="'.$data[$i]['login'].'">
              <div class="caption">
                <h3>'.$data[$i]['login'].'</h3>
                <p class="desc">Проверка пррка про п проверка!</p>
                <p><a href="#" class="btn btn-primary btn-xs" role="button">Выбрать</a></p>
              </div>
            </div>
          </div>';     

        }
        return $str;

    }
    
    public function buyRefOnBoard(){
        
        
        //debug($this->data);die;
        
        
        if($_SESSION['user']['balance'] >= 0){
            
            // занести в список реф стены
            
            $mod = new Refpage();
            
            
            
            $data = [
                'user_id'=>$_SESSION['user']['id'],
                'date_add'=>time()
                    ];
            
            if($mod->insert($data)){
                
                //$_SESSION['user']['balance'] -= 2;// вычесть из баланса и записать  в баланс
                
                $img = !empty($_SESSION['user']['img']) ? $_SESSION['user']['img'] : 'no-user-image.gif';
                
                $mycookie = ['img'=>$img];
                
                
                // зачислить на яндекс кошелек плату за услугу сайта
                
                
                

                $user = new User(); // обновление баланса
                $user->update([
                    'balance' => $_SESSION['user']['balance'],
                ],"`ip` = '".$_SESSION['user']['ip']."' AND `login` = '".$_SESSION['user']['login']."'");
                
                $this->respJson($this->sysMessage('success','Поздравляем! Ваш аватар размещен на стене рефереров'),false,false,$mycookie);
                
                
            }
            
            
            
        }else $this->respJson($this->sysMessage('danger','У Вас недостаточно средств на счёте!'));
        
        
        
    }
    
    public function actionAddreklam(){
        
        $this->title = 'Размещение рекламы';
        $this->meta_desc = 'Страница размещение рекламы мета описание';
        $this->meta_key = 'Страница размещение рекламы мета кей';
        
        
        
        $typeReklams = [
            
            0 => 'Статические ссылки',
            1 => 'Динамические ссылки',
            2 => 'Контекстные ссылки',
            3 => 'Текстовое объявление',
            
        ];
        
        $list = $this->getListServices($typeReklams);
        
        
        
        
        $this->render('addreklam',compact('list'));
    }
    
    public function getOrderFormStaticLink(){
        
        $head = '<h4>Разместить статическую ссылку</h4>';
        
        $desc = '<p>Размещаются на определенное количество дней. При этом количество переходов по ним может быть неограниченное. Вы получаете именно заинтересованных посетителей на свой проект. Ссылка появится на странице - Задания / Серфинг сайтов.</p>';

        $params = [
            
            0 => [
                
                'label' => 'URL сайта',
                'id' => 'url',
                'name' => 'url',
                'plh' => 'http://example.ru',
                
            ],
            1 => [
                
                'label' => 'Описание',
                'id' => 'desc',
                'name' => 'desc',
                'plh' => 'Описание рекламной ссылки',
                
            ],
            2 => [
                
                'label' => 'Количество дней показа',
                'id' => 'qntday',
                'name' => 'qntday',
                'plh' => '',
                'val' => '10',
                
            ],
            
        ];
        
        $button = ['addstaticlink','Создать статическую ссылку'];
        
        $select = [
            0 => [
                'label' => 'Выделение красным',
                'id' => 'linkselect',
            ],
            1 => [
                'Да (+ 5 руб./день)',
                'Нет'
            ]
        ];
        
        $sum = $this->getSumStaticLink(10,0); //на 10 дней и с выделением
        

        $param = [];
        $param[0] = 'orderForm';
        $param[1] = $head.$desc.$this->getForm($params,$button,$select,$sum);
        
        $this->respJson2($param);
    }
    
    public function getListServices($typeReklams){
        
        $str = '<ul>';
        
        for($i=0; $i<count($typeReklams); $i++){
            
            $str .= '<li><a href="#" id="serv'.$i.'">'.$typeReklams[$i].'</a></li>';
            
        }
        
        return $str.'</ul>';
    }
    
    public function getForm($params,$button,$select='',$sum='',$arrk=''){
        
        $view = new Viewcontroller();
        
        
        
        for($i=0; $i<count($params); $i++){
            
            $arr = [];
            
            foreach($params[$i] as $k => $v) $arr[$k] = $v;
            
            $inputs .= $view->prerender('inpform',$arr);
        }
        
        if(is_array($arrk)){
            
            for($j=0; $j<count($arrk); $j++) $selects .= $this->getSelect($select, $arrk[$j]);
            
        }else $selects = ($select) ? $this->getSelect($select) : '';
        
        return $view->prerender('reklform',[
                   'butid' => $button[0],
                   'butname'=> $button[1],
                   'inputs' => $inputs,
                   'selects' => $selects,
                   'sum' => $sum,
               ]);
    }
    
    public function getSelect($select, $k=''){
        
        $view = new Viewcontroller();
        
        if($k === ''){
            
            for($i=0; $i<count($select[1]); $i++){
            
                $sel = ($i) ? '' : 'selected';

                $str .= '<option '.$sel.' value="'.$i.'">'.$select[1][$i].'</option>';
            }
            return $view->prerender('select',[

                'label' => $select[0]['label'],
                'id' => $select[0]['id'],
                'opt' => $str,

            ]);
        }else{
            
            for($i=0; $i<count($select[$k][1]); $i++){
            
                if($k == 1 && $i == 6) $sel = 'selected';
                else $sel = ($i) ? '' : 'selected';

                $str .= '<option '.$sel.' value="'.$i.'">'.$select[$k][1][$i].'</option>';
            }
            return $view->prerender('select',[

                'label' => $select[$k][0]['label'],
                'id' => $select[$k][0]['id'],
                'opt' => $str,

            ]);
        }
    }
    
    public function addStaticLink(){// размещение статич ссылки рекламодателем
        
        $qntday = $this->data['qntday'];
        $opt = $this->data['opt'];
        
        $totsum = $this->getSumStaticLink($qntday,$opt);
        
        // проверить наличие средств на рекламном счёте
        if(!$this->validReklBalance($totsum)) $this->respJson($this->sysMessage('danger','Недостаточно средств!'));
        
        // списать средства с рекламного счета юзера
        if(!$this->UpdateReklBalance($totsum))
            $this->respJson($this->sysMessage('danger','Ошибка обновления рекламного счета!'));
        
        
        $mod = new Contextlinks();

        $res = $mod->insert([
            
            'user_id' => $_SESSION['user']['id'],
            'opt' => $this->data['opt'],
            'title' => $this->data['desc'],
            'url' => $this->data['url'],
            'h' => $this->data['h'],
            'period' => $this->data['qntday'] * 24 * 60 * 60,
            'date_add'=>time(),
            
        ]);
        
        if($res) $this->respJson($this->sysMessage('success','Статическая ссылка успешно добавлена!'));
        else $this->respJson($this->sysMessage('danger','Ошибка добавления ссылки в БД!'));
    }

    public function validReklBalance($sum){
        
        if(($_SESSION['user']['acnt2'] - $sum) < 0) return false;
        else return true;
    }
    
    public function UpdateReklBalance($sum){

        $u = new User();
        
        $_SESSION['user']['acnt2'] -= $sum;
        
        $res = $u->update([
            'acnt2' => $_SESSION['user']['acnt2'],
        ],'`id`='.$_SESSION['user']['id']);
        
        if($res) return true;
        return false;        
    }
    
    public function getSumStaticLink($qntday,$opt){// статич ссылка 20 руб в день
        
        $sumopt = ($opt) ? 0 : (5 * $qntday);
        
        return ($qntday * 20) + $sumopt;
    }
    public function getSumCntxtLink($qntserf, $opt){// контекст ссылка 0,5 руб за переход
        
        $sumopt = ($opt) ? 0 : (0.15 * $qntserf);
        
        return ($qntserf * 0.5) + $sumopt;
    }
    public function getSumTxtLink($qntday,$opt){// текст объвл 5 руб в день
        $sumopt = ($opt) ? 0 : (1 * $qntday); // 1 руб./день если выделено
        
        return ($qntday * 5) + $sumopt;
    }
    public function getSumSerfLink($qntserf,$optunlim,$opttime='',$opt=''){
        
        switch($optunlim){
            case 0: $sum0 = 1000;
                break;
            case 1: $sum0 = 800;
                break;
            case 2: $sum0 = 600;
                break;
            case 3: $sum0 = 400;
                break;
            case 4: $sum0 = 0;
                break;
        }
        
        if(empty($opttime)) $opttime = 0;
        
        switch($opttime){
            case 0: $sum1 = 0.06 * $qntserf;
                break;
            case 1: $sum1 = 0.06 * $qntserf;
                break;
            case 2: $sum1 = 0.06 * $qntserf;
                break;
            case 3: $sum1 = 0.06 * $qntserf;
                break;
            case 4: $sum1 = 0.06 * $qntserf;
                break;
            case 5: $sum1 = 0.06 * $qntserf;
                break;
            case 6: $sum1 = 0.06 * $qntserf;
                break;
        }
        
        if($opt) $sum2 = 0;
        else $sum2 = 0.01 * $qntserf;
        
        if($optunlim != 4) $total = $sum0;
        else $total = $sum0 + $sum1 + $sum2;

        return $total;
    }
    
    public function addCntxtLink(){// размещение контекстной ссылки рекламодателем
        
        $qntserf = $this->data['qntserf'];
        $opt = $this->data['opt'];
        
        $totsum = $this->getSumCntxtLink($qntserf,$opt);
        
        // проверить наличие средств на рекламном счёте
        if(!$this->validReklBalance($totsum)) $this->respJson($this->sysMessage('danger','Недостаточно средств!'));
        
        // списать средства с рекламного счета юзера
        if(!$this->UpdateReklBalance($totsum))
            $this->respJson($this->sysMessage('danger','Ошибка обновления рекламного счета!'));
        
        $mod = new Contextlinks();

        $res = $mod->insert([
            
            'user_id' => $_SESSION['user']['id'],
            'opt' => $this->data['opt'],
            'title' => $this->data['title'],
            'desc' => $this->data['desc'],
            'url' => $this->data['url'],
            'h' => $this->data['h'],
            'n' => $this->data['qntserf'],
            'date_add'=>time(),
            
        ]);
        
        if($res) $this->respJson($this->sysMessage('success','Контекстная ссылка успешно добавлена!'));
        else $this->respJson($this->sysMessage('danger','Ошибка добавления ссылки в БД!'));
    }

    public function getOrderFormCntxtLink(){
        
        $head = '<h4>Разместить контекстную ссылку</h4>';
        
        $desc = '<p>Главное преимущество контекстной рекламы - это максимально качественная аудитория. Пользователь не получает вознаграждения за клик по вашей рекламе, соответственно, если он кликнул - значит ваш ресурс его действительно заинтересовал. Ваша ссылка будет находиться в контексте сколь угодно долго. Вы платите только за фактический переход по ссылке. Реклама размещена на всех страницах сайта.</p>';

        $params = [
            
            0 => [
                
                'label' => 'URL сайта',
                'id' => 'url',
                'name' => 'url',
                'plh' => 'http://example.ru',
                
            ],
            1 => [
                
                'label' => 'Заголовок',
                'id' => 'title',
                'name' => 'title',
                'plh' => 'Заголовок ссылки',
                
            ],
            2 => [
                
                'label' => 'Описание',
                'id' => 'desc',
                'name' => 'desc',
                'plh' => 'Описание ссылки',
                
            ],
            3 => [
                
                'label' => 'Количество переходов',
                'id' => 'qntserf',
                'name' => 'qntserf',
                'plh' => '',
                'val' => '100',
                
            ],
            
        ];
        
        $button = ['addcntxtlink','Создать контекстную ссылку'];
        
        $sum = $this->getSumCntxtLink(100,0);// сумма на 100 просмотров и выделенная
        
        $select = [
            0 => [
                'label' => 'Выделение красным',
                'id' => 'cntxtselect',
            ],
            1 => [
                'Да (+ 0.15 руб./ за переход)',
                'Нет'
            ]
        ];
        
        
        
        
        $param = [];
        $param[0] = 'orderForm';
        $param[1] = $head.$desc.$this->getForm($params,$button,$select,$sum);
        
        $this->respJson2($param);
    }
    
    public function getOrderFormSerfLink(){
        
        
        $head = '<h4>Разместить динамическую ссылку</h4>';
        
        $desc = '<p>Это ссылки с определенным количеством переходов по ним. Вы гарантировано получите заказанное количество посетителей на свой сайт. Все посетители по вашей ссылке будут уникальными в течении 24 часов. Ссылка размещается на странице Работа / Серфинг.</p>';

        $params = [
            
            0 => [
                
                'label' => 'URL сайта',
                'id' => 'url',
                'name' => 'url',
                'plh' => 'http://example.ru',
                
            ],
            1 => [
                
                'label' => 'Заголовок',
                'id' => 'title',
                'name' => 'title',
                'plh' => 'Заголовок ссылки',
                
            ],
            2 => [
                
                'label' => 'Описание',
                'id' => 'desc',
                'name' => 'desc',
                'plh' => 'Описание ссылки',
                
            ],
            3 => [
                
                'label' => 'Количество просмотров (в сут.)',
                'id' => 'qntserflink',
                'name' => 'qntserflink',
                'plh' => '',
                'val' => '500',
                
            ],
            
        ];
        
        $button = ['addserflink','Создать динамическую ссылку'];
        
        $sum = $this->getSumSerfLink(500,0);// сумма на 500 просмотров и выделенная, безлимит
        
        
        $select = [
            0 => [    
                0 => [
                    'label' => 'Безлимитка',
                    'id' => 'unlimselect',
                ],
                1 => [
                    'Да (на 30 дней) 1000 руб.',
                    'Да (на 3 недели) 800 руб.',
                    'Да (на 2 недели) 600 руб.',
                    'Да (на 1 неделю) 400 руб.',
                    'Нет'
                ]
            ],
            1 => [    
                0 => [
                    'label' => 'Время просмотра',
                    'id' => 'timeviewselect',
                ],
                1 => [
                    '20 сек. (0.06 руб./ за 1 просмотр)',
                    '25 сек. (0.06 руб./ за 1 просмотр)',
                    '30 сек. (0.06 руб./ за 1 просмотр)',
                    '35 сек. (0.06 руб./ за 1 просмотр)',
                    '40 сек. (0.06 руб./ за 1 просмотр)',
                    '50 сек. (0.06 руб./ за 1 просмотр)',
                    '60 сек. (0.06 руб./ за 1 просмотр)',
                ]
            ],
            2 => [    
                0 => [
                    'label' => 'Выделение красным',
                    'id' => 'serfselect',
                ],
                1 => [
                    'Да (+ 0.01 руб./ за просмотр)',
                    'Нет'
                ]
            ]
        ];
        
        $arrk = [0,1,2];// порядок вывода селектов
        
        $param = [];
        $param[0] = 'orderForm';
        $param[1] = $head.$desc.$this->getForm($params,$button,$select,$sum,$arrk);
        
        $this->respJson2($param);
    }
    
    public function getOrderFormTextLink(){
        
        $head = '<h4>Разместить текстовое объявление</h4>';
        
        $desc = '<p>Текстовые объявления на Profit-System размещаются посуточно. При этом количество переходов по ним может быть неограниченное. Вы получаете именно заинтересованных посетителей на свой проект. Ссылка размещается на страницах Работа / Серфинг сайтов.</p>';

        $params = [
            
            0 => [
                
                'label' => 'URL сайта',
                'id' => 'url',
                'name' => 'url',
                'plh' => 'http://example.ru',
                
            ],
            1 => [
                
                'label' => 'Ваше объявление',
                'id' => 'desc',
                'name' => 'desc',
                'plh' => 'Текст объявления',
                
            ],
            2 => [
                
                'label' => 'Количество дней показа',
                'id' => 'qnttxtday',
                'name' => 'qntday',
                'plh' => '',
                'val' => '30',
                
            ],
            
        ];
        
        $button = ['addtxtlink','Создать текстовое объявление'];
        
        $sum = $this->getSumTxtLink(30,0);// сумма на 30 дн показа и выделенная
        
        $select = [
            0 => [
                'label' => 'Выделение красным',
                'id' => 'txtselect',
            ],
            1 => [
                'Да (+ 1 руб./день)',
                'Нет'
            ]
        ];
        
        $param = [];
        $param[0] = 'orderForm';
        $param[1] = $head.$desc.$this->getForm($params,$button,$select,$sum);
        
        $this->respJson2($param);
    }
    
    
    
    public function addDynamLink(){// размещение динамической ссылки рекламодателем (серфинг)

        $qntserf = $this->data['qntserf'];
        $optunlim = $this->data['optunlim'];// выбор безлимитки
        $opttime = $this->data['opttime'];// выбор времени и цены
        $opt = $this->data['opt'];  // выделять КРАСНЫМ или нет
        
        $totsum = $this->getSumSerfLink($qntserf,$optunlim,$opttime,$opt);
        
        // проверить наличие средств на рекламном счёте
        if(!$this->validReklBalance($totsum)) $this->respJson($this->sysMessage('danger','Недостаточно средств!'));
        
        // списать средства с рекламного счета юзера
        if(!$this->UpdateReklBalance($totsum))
            $this->respJson($this->sysMessage('danger','Ошибка обновления рекламного счета!'));

        $this->getTimerAndPrice($this->data['opttime'], $timer, $price);
        
        $period = $this->getPeriod($this->data['optunlim']);
        
        $mod = new Serfing();

        $res = $mod->insert([
            
            'user_id' => $_SESSION['user']['id'],
            'opt' => $this->data['opt'],
            'title' => $this->data['title'],
            'desc' => $this->data['desc'],
            'url' => $this->data['url'],
            'h' => $this->data['h'],
            'k' => 1,// пошли первые сутки действия ссылки
            'n' => $this->data['qntserf'],
            'period' => $period,
            'timer' => $timer,
            'price' => $price,
            'date_add'=>time(),
            
        ]);
        
        if($res) $this->respJson($this->sysMessage('success','Динамическая ссылка успешно добавлена!'));
        else $this->respJson($this->sysMessage('danger','Ошибка добавления ссылки в БД!'));
    }
    
    public function getTimerAndPrice($opttime, &$timer, &$price){
        switch($opttime){
            case 0: $timer = 20; $price = 0.04; break;// время и цена СЕРФссылки для пользователя
            case 1: $timer = 25; $price = 0.04; break;
            case 2: $timer = 30; $price = 0.04; break;
            case 3: $timer = 35; $price = 0.04; break;
            case 4: $timer = 40; $price = 0.04; break;
            case 5: $timer = 50; $price = 0.04; break;
            case 6: $timer = 60; $price = 0.04; break;
        }
    }
    public function getPeriod($optunlim){
        
        switch($optunlim){ // период показа ссылки   
            case 0: $period = 30 * 24 * 60 * 60; break;
            case 1: $period = 21 * 24 * 60 * 60; break;
            case 2: $period = 14 * 24 * 60 * 60; break;
            case 3: $period = 7 * 24 * 60 * 60; break;
            case 4: $period = 24 * 60 * 60; break;// по умолчанию на суткм
        }
        return $period;
    }
    
    
    public function addTxtLink(){// размещение текстовой ссылки рекламодателем

        $totsum = $this->getSumTxtLink($this->data['qntday'],$this->data['opt']);
        
        // проверить наличие средств на рекламном счёте
        if(!$this->validReklBalance($totsum))
            $this->respJson($this->sysMessage('danger','Недостаточно средств!'));
        
        // списать средства с рекламного счета юзера
        if(!$this->UpdateReklBalance($totsum))
            $this->respJson($this->sysMessage('danger','Ошибка обновления рекламного счета!'));
        
        $mod = new Textlinks();

        $res = $mod->insert([
            
            'user_id' => $_SESSION['user']['id'],
            'opt' => $this->data['opt'],
            'desc' => $this->data['desc'],
            'url' => $this->data['url'],
            'h' => $this->data['h'],
            'period' => $this->data['qntday'] * 24 * 60 * 60,
            'date_add'=>time(),
            
        ]);
        
        if($res) $this->respJson($this->sysMessage('success','Текстовое объявление успешно добавлено!'));
        else $this->respJson($this->sysMessage('danger','Ошибка добавления ссылки в БД!'));
    }
    
    


    
    
    
    
    
    
    
    
    
    
    

    public function actionLogin(){
        
        $this->title = 'Страница авторизации';
        $this->meta_desc = 'Страница авторизации мета описание';
        $this->meta_key = 'Страница авторизации мета кей';
        
        if(isset($_SESSION['user'])) $this->redirect('profile');
        else $this->render('login');
        
    }
    
    public function actionLogout(){

        session_destroy();
        //unset($_SESSION['flash']);

        
        $this->redirect('/');
    }
    
    public function getPageOtherUser($id){
        
        $u = new User();
        
        $f = '`login`,`ref_id`,`img`,`status`,`rating`,`balance`,`b`,`date_reg`,`date_act`';
        
        $data = $u->find($f, '`id`='.$id);
        
        $login = $data[0]->login;
        
        $img = $data[0]->img ? $data[0]->img : 'no-user-image.gif';
        
        $status = $data[0]->status;
        
        $rating = $data[0]->rating;
        
        $balance = $data[0]->balance;

        $referer = ($data[0]->ref_id) ? $this->getLoginOnID($data[0]->ref_id) : 'нет';
            
        $toberef = !$_SESSION['user']['ref_id'] ? '<a href="###" id="addref" class="btn btn-success btn-xs" role="button">Стать его рефералом</a>' : '';
        
        $b = $data[0]->b;
            
        $date_reg = $data[0]->date_reg;
        
        $date_act = $data[0]->date_act;
        
        
        $this->title = 'Страница '.$login;
        $this->meta_desc = 'Страница профиля мета описание';
        $this->meta_key = 'Страница профиля мета кей';
            
        $this->render('profile',compact('img','login','status','rating','balance','referer','toberef','b','date_reg','date_act')); 
        
    }
    
    public function actionProfile($id){
        
        
        

        if(!isset($_SESSION['user'])) $this->redirect('login');
        else{
            
            if(is_numeric($id) && preg_match('/^\d{1,10}$/',$id)){
            
                $id = (int)$id;

                if($id != $_SESSION['user']['id']){

                    $this->getPageOtherUser($id);
                    return;
                }
            }
            
            
            
            $img = ($_SESSION['user']['img']) ? $_SESSION['user']['img'] : 'no-user-image.gif';
            $login = $_SESSION['user']['login'];
            $status = $_SESSION['user']['status'];
            
            $rating = number_format($_SESSION['user']['rating'],2,',',''); ///   текущий рейтинг
            
            $balance = number_format($_SESSION['user']['balance'], 3, ',', ' ');
            $ac2 = number_format($_SESSION['user']['acnt2'], 2, ',', ' ');
            
            $referer = ($_SESSION['user']['ref_id']) ? $this->getLoginOnID($_SESSION['user']['ref_id']) : 'нет';
            $ref_b = ($_SESSION['user']['ref_id']) ? $_SESSION['user']['ref_b'].'%' : '';
            $date_ref = ($_SESSION['user']['ref_id']) ? $this->formDate($_SESSION['user']['date_ref']) : '';
            
            $b = $_SESSION['user']['b'];
            $date_reg = $_SESSION['user']['date_reg'];
            $date_act = $_SESSION['user']['date_act'];
            
            $ip = (strpos($_SESSION['user']['ip'],'_') !== false) ? $_SESSION['user']['ip'] : long2ip($_SESSION['user']['ip']);
            
            $ref_url = 'http://'.$_SERVER['HTTP_HOST'].'/registration/'.$_SESSION['user']['id'];
            $email = $_SESSION['user']['email'];
            $wal = $_SESSION['user']['wallet'];
            
            if(!empty($email)) $text = 'Изменить';
            else $text = 'Добавить';

            $this->title = 'Страница '.$login;
            $this->meta_desc = 'Страница профиля мета описание';
            $this->meta_key = 'Страница профиля мета кей';
            
            $usersettings = $this->view->prerender('settings',compact('ip','wal','ref_url','email','text'));
            $userstats = $this->view->prerender('stats');
            
            $this->render('profile',compact('img','login','status','rating','balance','ac2','referer','ref_b','date_ref','b','date_reg','date_act','usersettings','userstats'));
        }
    }
    
    public function addNewReferal(){
        
        if($this->data['ref_id']){
            
            if(!$_SESSION['user']['ref_id']){

                $lg = $this->getLoginOnID($this->data['ref_id']);
                
                $_SESSION['user']['date_ref'] = time();

                $u = new User();
            
                $res = $u->update([
                    'ref_id'=>$this->data['ref_id'],
                    'date_ref'=>$_SESSION['user']['date_ref'],
                    'ref_b'=>$this->r_b // рефбэк пользователя
                ],"`login`='".$_SESSION['user']['login']."'");
                
                $_SESSION['user']['ref_id'] = $this->data['ref_id'];

                if($res) $this->respJson($this->sysMessage('success','Вы прикреплены к рефереру <b>'.$lg.'</b>!'));                
            }
        }
        $this->respJson($this->sysMessage('danger','Ошибка добавления нового реферала!'));
        
    }
    
    public function actionRegistration($id){
        
        if(is_numeric($id) && preg_match('/^\d{1,10}$/',$id)) $ref_id = (int)$id;
        
        $this->title = 'Страница регистрациии';
        $this->meta_desc = 'Страница регистрации мета описание';
        $this->meta_key = 'Страница регистрации мета кей';
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // получать по три реферера из стены рефереров с каждым открытием этой страницы
        if(!isset($ref_id)){
            
            $mod = new User();
        
            if(isset($_SESSION['sys']['ref_cnt']) && $_SESSION['sys']['ref_cnt'] != 15){
                
                $_SESSION['sys']['ref_cnt'] = $_SESSION['sys']['ref_cnt'] + 3;
                $offset = $_SESSION['sys']['ref_cnt'];
            }else{
                $_SESSION['sys']['ref_cnt'] = 0;
                $offset = 0;
            }

            $data = $mod->getRefPageData();
            $c = count($data);

            if($c <= 3) $offset = 0;
            else{
                $r = $offset - $c;
                if($r == 3 && $c != 3) $offset = 3;

                if($r < 3 && $r >= 0) $offset = 0;

                if($r > 3){
                    if($r == 6 || $r == 7 || $r == 8){

                        if($offset == 12) $offset = 0;
                        if($offset == 15) $offset = 6;
                    }else $offset = 3;
                }
            }
            $newdata = array_slice($data,$offset,3);

            if(!empty($newdata)){

                $txt = 'Выберите одного реферера и на ваш баланс поступит бонус 2,00 руб.!';
                $refers = '<p>'.$txt.'</p><div class="row">'.$this->getHtmlRefData($newdata).'</div>';
            }
        }
        
        
        $this->render('regist',compact('ip','ref_id','refers'));
    }
    
    
    
    
    
    public function addSerfView(){
        $wctrl = new WorksController();
        $wctrl->addSerfView();
    }
    public function addViewStaticLink(){
        $wctrl = new WorksController();
        $wctrl->addViewStaticLink();
    }
    public function addViewCntxtLink(){
        $wctrl = new WorksController();
        $wctrl->addViewCntxtLink();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}



?>