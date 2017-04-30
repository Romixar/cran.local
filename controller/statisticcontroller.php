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
    
    public function actionMystats(){
        
        $this->title = 'Страница моя статистика';
        $this->meta_desc = 'Страница моя статистика мета описание';
        $this->meta_key = 'Страница моя статистика мета кей';
        
        
        // собирать статистику по заработку
        
        $mod = new History_s();
        
        $data = $mod->find('*','`user_id`='.$_SESSION['user']['id']);
        
        
        $content = $this->getHTMLTabSerfing($data,['Дата','Кол-во','Сумма']);
        
        
        
        
        $names = $this->getTabs('class="active"','names');
        $tabs = $this->getTabs(' in active','tabs',$content);
        
        
        $this->render('my_stats',compact('names','tabs'));
    }
    
    public function getHTMLTabSerfing($data,$arrhead){

        $ths = $this->getTableHead($arrhead);
        
        for($i=0; $i<count($data); $i++){
            
            $qnt = count(explode(',',substr($data[$i]->serf_ids,0,-1)));
            
            
            
            
            $tds = '<td>'.$data[$i]->date_add.'</td><td>'.$qnt.'</td><td>'.$data[$i]->sum.'</td>';
            
            $trs .= '<tr>'.$tds.'</tr>';
        }
        
        
        
        
        return $this->view->prerender('table',compact('ths','trs'));
    }
    
    public function getTableHead($arrhead){
            
        for($j=0; $j<count($arrhead); $j++) $ths .= '<th>'.$arrhead[$j].'</th>';
            
        return $ths;
    }
    
    
    
    
    
    
}







?>