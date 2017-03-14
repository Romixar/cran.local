<?php

class NewsController extends Controller{
    
    public function actionIndex(){
        $this->title = 'Страница Новости';
        $this->meta_desc = 'Страница Новости мета описание';
        $this->meta_key = 'Страница Новосим мета кей';
        
//        $comm = new Comments();
//        
//        $data = $comm->find('*');
//        
//        $data = $this->getTreeComments($data);
        
        //debug($data);
        
        
        $this->render('news');
    }
    
    
    public function getTreeComments($data){
        $tree = [];// здесь будет дерево комментариев
        
        
        for($i=0; $i<count($data); $i++){
            
            $tree[$data[$i]->id] = $data[$i];
            
        }
        
        foreach($tree as $k => $v){
            
            for($j=0; $j<count($data); $j++){
                
                if($v->parent_id == $data[$j]->id){
                    
                    $tree[$data[$j]->id]->childs = [
                        $data[$j]->id,
                        $data[$j]->parent_id,
                        $data[$j]->name,
                        $data[$j]->text,
                        $data[$j]->date_add
                                                   ];
                    
                }
                
            }
            
            
            //if(isset($v->childs)) 
            
        }
        
        
        
        
        
        return $tree;
    }
    
    
}

?>