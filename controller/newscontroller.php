<?php

class NewsController extends Controller{
    
    public function actionIndex(){
        $this->title = 'Страница Новости';
        $this->meta_desc = 'Страница Новости мета описание';
        $this->meta_key = 'Страница Новосим мета кей';
        
        $comm = new Comments();
        
        $data = $comm->findComments('*');

        $data = $this->getTreeComments($data);
        
        //debug($data);
        
        foreach($data as $id => $comment){
            
            $str .= $this->getHTMLComments($comment);
                
            if($comment['childs']){
                
                foreach($comment['childs'] as $id1 => $comment1){
                    
                    $str .= $this->getHTMLComments($comment1);
                    
                }
                
                
    
            }
            
            
        }
        
        
        
        
        
        
        
        
        debug($str);
        
        $this->render('news');
    }
    
    public function getHTMLComments($comment){
        
            
        $str .= '<li id="comm_'.$comment['id'].'">';
            
        $str .= $this->view->prerender('comments', compact('comment'));
                
        if($comment['childs']){
                
            foreach($comment['childs'] as $id1 => $comment1){
                    
                $str .= '<ul class="childs chldcomm_'.$comment['id'].'">';

                $str .= $this->view->prerender('comments', ['comment'=>$comment1]);
        
                $str .= '</ul>';
                    
            }   
        }
            
        $str .= '</li>';
                
        
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