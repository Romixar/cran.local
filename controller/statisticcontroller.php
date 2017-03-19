<?php

class StatisticController extends Controller{
    
    
    
    public function actionIndex(){
        $this->title = 'Страница Статистика';
        $this->meta_desc = 'Страница статистика мета описание';
        $this->meta_key = 'Страница статистика мета кей';
        
        $his_b = new History_b();
        $data = $his_b->getBonusRating();
        
        for($i=0; $i<count($data); $i++){
            $data[$i]['max(`date_add`)'] = $this->formDate($data[$i]['max(`date_add`)']);
        }
        
        $rating = $this->getHTMLTabRating($data);
        
        
        $this->render('statistic',compact('rating'));
    }
    
    public function getHTMLTabRating($data){
        
        $str = '';
        for($i=0; $i<count($data); $i++){
            
            
            $str .= '<tr><td>'.($i+1).'.</td><td>'.$data[$i]['login'].'</td><td>'.$data[$i]['max(`date_add`)'].'</td><td>'.$data[$i]['b'].'</td><td>'.$data[$i]['sum(`sum`)'].'</td></tr>';
            
        }
        
        return $str;
        
        
    }
    
    
    
    
    
    
}







?>