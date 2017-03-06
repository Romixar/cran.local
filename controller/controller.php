<?php

class Controller{
    
    public $view;// объект видов
    public $btn = []; // кнопка авторизации
    
    public $sysmes = ''; // системные сообщения
    public $data; // POST массив
    
    public $title;
    public $meta_desc;
    public $meta_key;
    
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
        
            
        $this->sysmes = Session::flash('sysmes');
        
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
        if(isset($data['do_message_f'])) $this->sendEmail();

        
    }
    

    
    
    
    
    
    public function actionLogin(){
        $this->title = 'Страница авторизации';
        $this->meta_desc = 'Страница авторизации мета описание';
        $this->meta_key = 'Страница авторизации мета кей';
        
        if(isset($_SESSION['user'])) $this->redirect('profile');
        else $this->render('login');
        
    }
    
    public function actionLogout(){
        
        session_destroy();
        $this->redirect('/');
    }
    
    public function validateLogin(){// поиск пользов-ля в БД
        $view = new Viewcontroller();
        $user = new User();
        if($user->findUser($this->data)) exit('{"redirect":"profile"}');// пройдена авторизация
        else{
            
            $type = 'danger';
            $mes = 'Авторизация не пройдена!';
                
            $sysmes = $view->prerender('message',compact('type','mes'));
            
            // сообщение о непройденной авторизации
            // асинхронно вывожу сообщение
            echo json_encode(['sysmes'=>$sysmes]);
            exit();                
        }
        
        
    }
    
    public function validateRegData(){
        $view = new Viewcontroller();
        $user = new User();
        ///if($user->validateIp($this->data)) echo json_encode(['sysmes'=>'Пользователь с вашим IP уже существует<br/>Хотите зарегистрировать второго?','btn'=>true]);
        
        if($user->validateIp($this->data)){
            
            $type = 'danger';
            $mes = 'Пользователь с вашим IP уже существует<br/>Хотите зарегистрировать второго?';
            $sysmes = $view->prerender('message',compact('type','mes'));
            
            echo json_encode(['sysmes'=>$sysmes,'btn'=>true]);
            
        }
        else{
            if($pos = strpos($this->data['ip'],'_0'))
                $this->data['ip'] = substr($this->data['ip'],0,$pos);
            $this->data['balance'] = 0;
            $this->data['date_reg'] = date('d-m-Y',time());
            $this->data['date_act'] = date('d-m-Y',time());
            
            //debug($this->data);die;
            
            // проверка рекапча
            $secret = '6LfvuRcUAAAAAOnEtZTBkEbVtKeqmU6vgcqIJx3a';
            //$response = $this->data['g-recaptcha-response'];// отправить POST запрос в гугл
            $response = $this->data['g-recaptcha-response'];// отправить POST запрос в гугл
            $remoteip = $_SERVER['REMOTE_ADDR'];
            
            $obj = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response.'&remoteip='.$remoteip);// обработка гугл капчи
            
            $res = json_decode($obj,true);// верну в виде массива ответ гугла
            
            if($res['success']){
                
                if($user->save($this->data)){
                
                    $type = 'success';
                    $mes = 'Вы успешно зарегистрировались!';

                    $sysmes = $view->prerender('message',compact('type','mes'));


                    // создать сообщ об успешной регистрации
                    Session::flash('sysmes',$sysmes);

                    exit('{"redirect":"profile"}');
                }
            }else{
                $type = 'danger';
                $mes = 'Неверно заполнена капча';
                $sysmes = $view->prerender('message',compact('type','mes'));

                echo json_encode(['sysmes'=>$sysmes]);
            }
        }
        exit();
    }
    
    public function actionProfile(){

        
        if(!isset($_SESSION['user'])) $this->redirect('login');
        else{
            
            $login = $_SESSION['user']['login'];
            $balance = number_format($_SESSION['user']['balance'], 3, ',', ' ');
            $date_reg = $_SESSION['user']['date_reg'];
            $date_act = $_SESSION['user']['date_act'];
            $ip = $_SESSION['user']['ip'];
            

            $this->title = 'Страница '.$login;
            $this->meta_desc = 'Страница профиля мета описание';
            $this->meta_key = 'Страница профиля мета кей';
            
            $this->render('profile',compact('login','balance','date_reg','date_act','ip')); 
        }
        
    }
    
    public function actionRegistration(){
        $this->title = 'Страница регистрациии';
        $this->meta_desc = 'Страница регистрации мета описание';
        $this->meta_key = 'Страница регистрации мета кей';
        
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $this->render('regist',compact('ip'));
    }
    
    public function sendEmail(){
        $view = new Viewcontroller();
        
        $title = 'Сообщение с сайта cran.local';
        $name = $this->data['name'];
        $uemail = $this->data['email'];
        $text = nl2br($this->data['message']);
        
        $body = $view->prerender('mail',compact('title','name','uemail','text'));
        $email = Config::$admEmail;
        
        $head = 'From: admin@zolushka18.ru'."\r\n".'MIME-Version 1.0'."\r\n".'Content-type: text/html; charset=UTF-8';
        
        mail($email,$title,$body,$head);
        
        exit();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function redirect($uri){
        header('Location: '.$uri);
    }
    
    
    
    
    public function render($tmpl,$data=[]){
        $title = $this->title;
        $meta_desc = $this->meta_desc;
        $meta_key = $this->meta_key;
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender($tmpl,$data);
        
        $right = $this->view->prerender('right',$this->btn);

        $sysmes = ($this->sysmes) ? $this->sysmes : '';

        
        $this->view->render('main', compact('title','meta_desc','meta_key','left','sysmes','content','right'));
        
    }
    
    
    
}








?>