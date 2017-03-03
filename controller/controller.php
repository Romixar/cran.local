<?php

class Controller{
    
    public $view;// объект видов
    public $btn = []; // кнопки авторизации
    
    public function __construct(){
        
        if(isset($_POST['do_login_f'])){
            $user = new User();
            $res = $user->findUser($this->xss($_POST));
            exit('{"redirect":"profile"}');
            //if($res) header('Location: /profile');
        }

        
        $this->view = new ViewController();
        
        if(!isset($_SESSION['user'])){
            $text = 'ВОЙТИ';
            $uri = 'login';
        }else{
            $text = 'ВЫЙТИ';
            $uri = 'logout';
        }
        $this->btn = compact('text','uri');
        
    }
    
    public function xss($data){

		$req = '/script|http|www\.|\'|\`|SELECT|UNION|UPDATE|exe|exec|CREATE|DELETE|INSERT|tmp/i';
			
		foreach($data as  $key => $val){
            
            $val = trim($val);//очистка от пробелов
				
			$val = preg_replace($req,'',$val);
                
            $data[$key] = htmlspecialchars($val);//все HTML теги в сущности
			
        }
        return $data;

        
    }
    

    
    
    
    public function actionIndex(){
        $left = $this->view->prerender('left');
        
        //if(!isset($_SESSION['user'])) LoginController::Auth();
        $attr = $this->btn;
        
        $right = $this->view->prerender('right',$attr);
        
        $this->view->render('main',compact('left','content','right'));
        
    }
    
    public function actionLogin(){
        
        //debug($_POST);
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender('login');
        
        $attr = $this->btn;
        
        $right = $this->view->prerender('right',$attr);

        
        
        $this->view->render('main',compact('left','content','right'));
    }
    
    public function actionProfile(){
        
        $this->render('profile');
        
        
        
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function render($tmpl,$data=[]){
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender($tmpl);
        
        
        
        $right = $this->view->prerender('right');

        
        
        $this->view->render('main',compact('left','content','right'));
        
    }
    
    
    
}








?>