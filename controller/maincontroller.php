<?php

class MainController extends Controller{
    

        
    
    
    public function actionIndex(){
        $this->title = 'Главная страница';
        $this->meta_desc = 'Главная страница мета описание';
        $this->meta_key = 'Главная страница мета кей';


        
        $b_tpl = $this->view->prerender('bonus',compact('bonus'));
        
        
        
        
        $this->render('index',compact('b_tpl'));
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
    
    public function delLastRef($data){
        
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
        
        $f = '`login`,`img`,`status`,`rating`,`balance`,`b`,`date_reg`,`date_act`';
        
        $data = $u->find($f, '`id`='.$id);
        
        $login = $data[0]->login;
        
        $img = $data[0]->img ? $data[0]->img : 'no-user-image.gif';
        
        $status = $data[0]->status;
        
        $rating = $data[0]->rating;
        
        $balance = $data[0]->balance;
            
        $referer = ($data[0]->referer) ? $data[0]->referer : 'нет';
            
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
            
            // надо получить логин реферера
            $referer = ($_SESSION['user']['ref_id']) ? $_SESSION['user']['ref_id'] : 'нет';
            
            
            
            
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
            
            $this->render('profile',compact('img','login','status','rating','balance','referer','b','date_reg','date_act','usersettings','userstats'));
        }
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}



?>