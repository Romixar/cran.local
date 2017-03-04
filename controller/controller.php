<?php

class Controller{
    
    public $view;// объект видов
    public $btn = []; // кнопка авторизации
    
    public $sysmes; // системные сообщения
    public $data; // POST массив
    
    public function __construct(){
        
        if(isset($_POST)) $this->xss($_POST);
        if(isset($_GET)) $this->xss($_GET);
        
        
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
        $this->data = $data;
        if(isset($data['do_login_f'])) $this->validateLogin();
        if(isset($data['do_regist_f'])) $this->validateRegData();

        
    }
    

    
    
    
    public function actionIndex(){
        
        
        
        $this->render('index');
    }
    
    public function actionLogin(){
        
        if(isset($_SESSION['user'])) $this->redirect('profile');
        else $this->render('login');
        
    }
    
    public function actionLogout(){
        
        session_destroy();
        $this->redirect('/');
    }
    
    public function validateLogin(){// поиск пользов-ля в БД
        
        $user = new User();
        if($user->findUser($this->data)) exit('{"redirect":"profile"}');// пройдена авторизация
        else{
            
            echo json_encode(['sysmes'=>'авторизация не пройдена!']);
            exit();
                // сообщение о непройденной авторизации
                // асинхронно вывожу сообщение
        }
        
        
    }
    
    public function validateRegData(){

        $user = new User();
        if($user->validateIp($this->data)) echo json_encode(['sysmes'=>'Пользователь с вашим IP уже существует<br/>Хотите зарегистрировать второго?','btn'=>true]);
        else{
            if($pos = strpos($this->data['ip'],'_0'))
                $this->data['ip'] = substr($this->data['ip'],0,$pos);
            $this->data['balance'] = 0;
            $this->data['date_reg'] = date('d-m-Y',time());
            $this->data['date_act'] = date('d-m-Y',time());
            if($user->save($this->data)){
                
                // создать сообщ об успешной регистрации
            
                exit('{"redirect":"profile"}');
                
            }   
        }
    }
    
    public function actionProfile(){
        
        if(!isset($_SESSION['user'])) $this->redirect('login');
        else{
            
            $login = $_SESSION['user']['login'];
            $balance = number_format($_SESSION['user']['balance'], 3, ',', ' ');
            
            


            $this->render('profile',compact('login','balance'));
            
        }
        
    }
    
    public function actionRegistration(){
        
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $this->render('regist',compact('ip'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function redirect($uri){
        header('Location: '.$uri);
    }
    
    
    
    
    public function render($tmpl,$data=[]){
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender($tmpl,$data);
        
        $right = $this->view->prerender('right',$this->btn);

        
        
        $this->view->render('main',compact('left','content','right'));
        
    }
    
    
    
}








?>