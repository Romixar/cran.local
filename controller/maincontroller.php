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
            $date_reg = $_SESSION['user']['date_reg'];
            $date_act = $_SESSION['user']['date_act'];
            $ip = $_SESSION['user']['ip'];
            $ref_url = 'http://'.$_SERVER['HTTP_HOST'].'/registration/'.$_SESSION['user']['id'];
            $email = $_SESSION['user']['email'];
            $wal = $_SESSION['user']['wallet'];
            
            if(!empty($email)) $text = 'Изменить';
            else $text = 'Добавить';

            $this->title = 'Страница '.$login;
            $this->meta_desc = 'Страница профиля мета описание';
            $this->meta_key = 'Страница профиля мета кей';
            
            $this->render('profile',compact('img','login','balance','date_reg','date_act','ip','ref_url','email','wal','text')); 
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