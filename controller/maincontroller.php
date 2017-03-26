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
        
        // удаление последнего реферера, еслт он уже есть
        
        if(count($data) > 2) unset($data[count($data)]);
        
        
        
        for($i=0; $i<count($data); $i++){
            
            $data[$i]['date_add'] = strftime('%d-%m-%Y %H:%M:%S',$data[$i]['date_add']);
            
        }
        
        debug($data);//die;
        
        
        
        
        $refpage = $this->getHtmlRefData($data);
        
        
        
        
        
        
        //debug($data);
        
        
        $this->render('ref_page',compact('refpage'));
    }
    
    public function getHtmlRefData($data){
        
        for($i=0; $i<count($data); $i++){
            
            $img = !empty($data[$i]['img']) ? $data[$i]['img'] : 'no-user-image.gif';
                
            $str .= '<div class="col-sm-4 col-md-4">
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
        
        
        if($_SESSION['user']['balance'] >= 2){
            
            // занести в список реф стены
            
            $mod = new Refpage();
            
            
            
            $data = [
                'user_id'=>$_SESSION['user']['id'],
                'date_add'=>time()
                    ];
            
            if($mod->insert($data)){
                
                $_SESSION['user']['balance'] -= 2;// вычесть из баланса и записать  в баланс
                
                // зачислить на яндекс кошелек плату за услугу сайта
                
                
                

                $user = new User(); // обновление баланса
                $user->update([
                    'balance' => $_SESSION['user']['balance'],
                ],"`ip` = '".$_SESSION['user']['ip']."' AND `login` = '".$_SESSION['user']['login']."'");
                
                $this->respJson($this->sysMessage('success','Поздравляем! Ваш аватар размещен на стене рефереров'));
                
                
            }
            
            
            
        }else $this->respJson($this->sysMessage('danger','У Вас недостаточно средств на рекламном счёте!'));
        
        
        
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
    
    public function actionProfile(){

        if(!isset($_SESSION['user'])) $this->redirect('login');
        else{
            
            $img = ($_SESSION['user']['img']) ? $_SESSION['user']['img'] : 'no-user-image.gif';
            $login = $_SESSION['user']['login'];
            $balance = number_format($_SESSION['user']['balance'], 3, ',', ' ');
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
            
            $this->render('profile',compact('img','login','balance','b','date_reg','date_act','ip','ref_url','email','wal','text')); 
        }
    }
    
    public function actionRegistration($id){
        
        if(is_numeric($id) && preg_match('/^\d{1,10}$/',$id)) $ref_id = (int)$id;

        
        
        $this->title = 'Страница регистрациии';
        $this->meta_desc = 'Страница регистрации мета описание';
        $this->meta_key = 'Страница регистрации мета кей';
        
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $this->render('regist',compact('ip','ref_id'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}



?>