<?php

class MainController extends Controller{
    
    
    
    
    public function actionIndex(){
        $this->title = 'Главная страница';
        $this->meta_desc = 'Главная страница мета описание';
        $this->meta_key = 'Главная страница мета кей';
        
        $this->render('index');
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
        
        parent::actionLogin();
        
    }
    
    public function actionProfile(){
        parent::actionProfile();
    }
    
    public function actionLogout(){
        
        parent::actionLogout();
        
    }
    
    public function actionRegistration(){
        
        parent::actionRegistration();
        
    }
    
    
}



?>