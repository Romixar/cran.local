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
        
        $data = $mod->find('*','`user_id`='.$_SESSION['user']['id'],'`date_add` DESC');
        
        
        $content = $this->getHTMLTabSerfing($data,['Дата','Кол-во','Сумма, руб.']);
        
        
        
        
        $names = $this->getTabs('class="active"','names');
        $tabs = $this->getTabs(' in active','tabs',$content);
        
        
        $this->render('my_stats',compact('names','tabs'));
    }
    
    public function getHTMLTabSerfing($data,$arrhead){

        $ths = $this->getTableHead($arrhead);
        
        for($i=0; $i<count($data); $i++){
            
            $qnt = count(explode(',',substr($data[$i]->serf_ids,0,-1)));
            
            $d = $this->formDate($data[$i]->date_add);
            
            
            $tds = '<td>'.$d.'</td><td>'.$qnt.'</td><td>'.$data[$i]->sum.'</td>';
            
            $trs .= '<tr>'.$tds.'</tr>';
        }
        
        
        
        
        return $this->view->prerender('table',compact('ths','trs'));
    }
    public function getHTMLTabReklSerfing($data,$arrhead){

        $ths = $this->getTableHead($arrhead);
        
        for($i=0; $i<count($data); $i++){
            
            $d = $this->formDate($data[$i]->date_add);
            $d_fin = $this->formDate($data[$i]->date_add + $data[$i]->period);
            $t = $data[$i]->title;
            $v = $data[$i]->v;
            $tot_v = $data[$i]->tot_v;
            
            $tds = '<td>'.$d.'</td><td>'.$d_fin.'</td><td>'.$t.'</td><td>'.$v.'</td><td>'.$tot_v.'</td>';
            
            $trs .= '<tr>'.$tds.'</tr>';
        }
        
        
        
        
        return $this->view->prerender('table',compact('ths','trs'));
    }
    
    public function getTableHead($arrhead){
            
        for($j=0; $j<count($arrhead); $j++) $ths .= '<th>'.$arrhead[$j].'</th>';
            
        return $ths;
    }
    
    
    public function actionMyreklams(){
        
        $this->title = 'Страница моя реклама';
        $this->meta_desc = 'Страница статистика по моей рекламе мета описание';
        $this->meta_key = 'Страница статистика по моей рекламе мета кей';
        
        
        // собирать статистику по поданной рекламе
        
        $mod = new Serfing();
        
        $f = 'id, user_id, date_add, title, period, tot_v, v';
        
        $data = $mod->find($f,'`user_id`='.$_SESSION['user']['id'],'`date_add` DESC');
        
        $arrHead = ['Дата подачи','Дата окончания','Название','Просм. за сут.','Просмотры всего'];
        
        $content = $this->getHTMLTabReklSerfing($data, $arrHead);
        
        
        
        
        $names = $this->getTabs('class="active"','names');
        $tabs = $this->getTabs(' in active','tabs',$content);
        
        
        $this->render('my_reklams',compact('names','tabs'));
        
        
        
        
        
        
    }
    
    
    
}







?>