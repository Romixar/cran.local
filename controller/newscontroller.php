<?php

class NewsController extends Controller{
    
    public function actionIndex(){
        $this->title = 'Страница Новости';
        $this->meta_desc = 'Страница Новости мета описание';
        $this->meta_key = 'Страница Новосим мета кей';
        
        $mod = new News();
        
        $news = $mod->find('*');
        
        $news = $this->getHTMLNews($news);
        
        $comm = new Comments();
        
        $data = $comm->findComments('*');

        $data = $this->getTreeComments($data);
        
        //debug($data);
        
        $str = $this->preComments($data);
        
        $form = ($_SESSION['user']) ? $this->view->prerender('form') : '';

        $this->render('news',['comments'=>$str,'form'=>$form,'news'=>$news]);
    }
    
    
    
    
    
    
    public function getHTMLNews($news){
        
        for($i=0; $i<count($news); $i++){
                
//            if($news[$i]->img){
                
                $a = '<a href="news/view/'.$news[$i]->id.'">';
                    
                $img = $a.'<img src="/images/4.jpg" class="img-responsive" alt="'.$news[$i]->title.'"></a>';
                
                
                
//            }else $img = '';
                
            $id = $news[$i]->id;
            $title = $news[$i]->title;
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