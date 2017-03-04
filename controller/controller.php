<?php

class Controller{
    
    public $view;// объект видов
    public $btn = []; // кнопки авторизации
    
    public $sysmes; // системные сообщения
    public $data; // POST массив
    
    public function __construct(){
        
        if(isset($_POST)) $this->data = $this->xss($_POST);
        
        
        if(isset($this->data['do_login_f'])) $this->validateLogin();
            

        
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
        
        $login = $_SESSION['user']['login'];
        
        $this->view->render('main',compact('left','content','right','login'));
        
    }
    
    public function actionLogin(){
        
        
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender('login');
        
        $attr = $this->btn;
        
        $right = $this->view->prerender('right',$attr);

        
        
        $this->view->render('main',compact('left','content','right'));
    }
    
    public function validateLogin(){// поиск пользов-ля в БД
        
        $user = new User();
        if($user->findUser($this->data)) exit('{"redirect":"profile"}');// пройдена авторизация
        else{
                // сообщение о непройденной авторизации
                // асинхронно вывожу сообщение
        }
        
        
    }
    
    public function actionProfile(){
        
        if(!isset($_SESSION['user'])) $this->actionLogin();
        else{
            
            $login = $_SESSION['user']['login'];
            $balance = $_SESSION['user']['balance'];


            $this->render('profile',compact('login','balance'));
            
        }
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function render($tmpl,$data=[]){
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender($tmpl,$data);
        
        
        
        $right = $this->view->prerender('right');

        
        
        $this->view->render('main',compact('left','content','right'));
        
    }
    
    
    
}








?>