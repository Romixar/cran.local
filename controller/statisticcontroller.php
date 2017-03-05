<?php

class StatisticController extends Controller{
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Статистика';
        $this->meta_desc = 'Страница статистика мета описание';
        $this->meta_key = 'Страница статистика мета кей';
        
        
        
        
        $this->render('statistic');
    }
    
    
    
    
}







?>