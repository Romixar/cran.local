<?php

class NewsController extends Controller{
    
    public function actionIndex(){
        $this->title = 'Страница Новости';
        $this->meta_desc = 'Страница Новости мета описание';
        $this->meta_key = 'Страница Новосим мета кей';
        
        
        
        $this->render('news');
    }
    
    
}

?>