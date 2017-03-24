<?php

class NewsController extends Controller{
    
    public function actionIndex($id){
        
        if(is_numeric($id) && preg_match('/^\d{1,10}$/',$id)) $page = (int)$id;
        
        $this->title = 'Страница Новости';
        $this->meta_desc = 'Страница Новости мета описание';
        $this->meta_key = 'Страница Новосим мета кей';
        
        $mod = new News();
        
        $lim = $this->pagination($page, $HTMLpages);
        
        
        $news = $mod->find('*','','',$lim);
        
        $news = $this->getHTMLNews($news);
        
        $comm = new Comments();
        
        $data = $comm->findComments('*');

        $data = $this->getTreeComments($data);
        
        //debug($data);
        
        $str = $this->preComments($data);
        
        $form = ($_SESSION['user']) ? $this->view->prerender('form') : '';

        $this->render('news',['comments'=>$str,'form'=>$form,'news'=>$news,'pagination'=>$HTMLpages]);
    }
    
    
    public function pagination($page, &$HTMLpages){
        
        $mod = new News();
        $totalPages = $mod->cntRow('id');
        
        $cntPages = round($totalPages / Config::$limit, 0, PHP_ROUND_HALF_UP);
        
        $HTMLpages = $this->getHTMLPagination($cntPages, $page);
        
        if(!empty($page)) $l = (Config::$limit * $page - Config::$limit).',';
        else $l = '';
        
        return $l.Config::$limit;
        
    }
    
    
    
    
    public function getHTMLPagination($cntPages, $act){
        
        if(!empty($act) && $act != 1){
            $u = $act-1;
            $c = '';
        }else{
            $u = '#';
            $c = 'class="disabled"';
        }
        
        $prev = '<li '.$c.'><a href="'.$u.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        
        
        for($i=0; $i<$cntPages; $i++){

            
            $cl = (($i+1) == $act) ? 'class="active"' : '';
            
            $str .= '<li '.$cl.'><a href="'.($i+1).'">'.($i+1).'</a></li>';
            
        }
        
        if(($act+1) <= $cntPages){
            $u = $act+1;
            $c = '';
        }else{
            $u = '#';
            $c = 'class="disabled"';
        }
        
        
        $next = '<li '.$c.'><a href="'.$u.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        
        
        $str = $prev.$str.$next;
        
        return $this->view->prerender('pagination',['pages'=>$str]);
        
    }
    
    public function getHTMLNews($news){
        
        for($i=0; $i<count($news); $i++){
                
            if($news[$i]->img){
                
                $a = '<a href="news/view/'.$news[$i]->id.'">';
                    
                $img = $a.'<img src="/images/'.$news[$i]->img.'" class="img-responsive post-img" alt="'.$news[$i]->title.'"></a>';
                
            }else $img = '';
                
            $id = $news[$i]->id;
            $title = $news[$i]->title;
            
            // форматир дату
            
            $date_add = $news[$i]->date_add;
            $preview = $news[$i]->preview;
            
            $str .= $this->view->prerender('preview',compact('id','title','date_add','img','preview'));
        }
        
        return $str;
        //debug($news);
        
    }
    

    
    public function getHTMLComments($comments){

            
        foreach($comments as $comment){
            $str .= $this->view->prerender('comments',compact('comment'));
            
            if($comment['childs']) $str .= $this->preComments($comment['childs']);
            
        }
        return $str;    
    }
    
    public function preComments($data){
        
        foreach($data as $comment){
            //id="comm_'.$comment['id'].'"
            $str .= '<li>';
            
            $str .= $this->view->prerender('comments',compact('comment'));
                
            if($comment['childs']){
                
                $str .= '<ul class="childs chldcomm_'.$comment['id'].'">';

                $str .= '<li>';
                $str .= $this->getHTMLComments($comment['childs']);
                $str .= '</li>';
                
                $str .= '</ul>';
            }
            
            $str .= '</li>';
        }
        return $str;
        
    }
    
    
    
    
    
    
    
    public function getTreeComments($data){
        $tree = [];// здесь будет дерево комментариев
        $tree1 = [];// здесь будет дерево комментариев
        
        for($j=0; $j<count($data); $j++){// удалю дублир. строки
            
            unset($data[$j][0]);
            unset($data[$j][1]);
            unset($data[$j][2]);
            unset($data[$j][3]);
            unset($data[$j][4]);
            unset($data[$j][5]);
            
        }
        
        for($i=0; $i<count($data); $i++){// делаю нумерацию по номерам ID массива комментов
            
            $tree[$data[$i]['id']] = $data[$i];
            
        }
        
        foreach($tree as $k => &$v){
            
            if($v['parent_id'] == 0) $tree1[$k] = &$v;
            else $tree[$v['parent_id']]['childs'][$k] = &$v;
                
        }
        return $tree1;
    }
    
    
}





















?>