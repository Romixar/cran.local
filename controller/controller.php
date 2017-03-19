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
            $id = 'u_in';
        }else{
            $text = 'ВЫЙТИ';
            $uri = 'logout';
            $id = 'u_out';
        }
        $this->btn = compact('text','uri','id');
        
            
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
        if(isset($data['do_login_f'])) $this->validateLogin(); // логин / пароль при авторизации
        if(isset($data['do_regist_f'])) $this->validateRegData(); // все данные польз-ля
        if(isset($data['do_message_f'])) $this->sendEmail();
        if(isset($data['reg_login_f'])) $this->validateRegLogin();// логин при регистрации
        if(isset($data['email'])) $this->validateAuthData();// email/file авториз-го (JSON пришел)
        if(isset($data['get_ref_list_f'])) $this->getRefList();// email авторизованного
        if(isset($data['get_b_list_f'])) $this->getBList();// запрос списка бонусов
        if(isset($data['get_bonus_f'])) $this->checkResponseBonus();// запрос бонуса

        
    }
    


    
    public function validateAuthData(){
        
        if(empty($_FILES['avatar']['name']) && empty($this->data['email'])) exit();
        
        $user = new User();
        
        if(!empty($_FILES['avatar']['name'])){
            
            if($res = $user->findImg($_FILES['avatar']['name'])){
                $sysmes = $this->sysMessage('danger','Файл с таким названием уже существует!');
                $this->respJson($sysmes);
            }
                
            
            if(!$uplfile = $this->validateFiles()){// валидация и сохр-е файла
                
                $sysmes = $this->sysMessage('danger','Неизвестная ошибка сохранения файла!');
                $this->respJson($sysmes);
            }
            $this->data['img'] = $uplfile;
            $_SESSION['user']['img'] = $uplfile;
        }

        
        if(!empty($this->data['email'])){
            
            if(!isset($res)) $res = $user->findEmail($this->data['email']);

            if($res != false){

                if($res[0]->id == $_SESSION['user']['id']){

                    if(!isset($this->data['img'])){
                        $sysmes = $this->sysMessage('success','Ваш E-mail подтверждён!');
                        $this->respJson($sysmes);
                    }
                        
                        
                }else{
                    $sysmes = $this->sysMessage('danger','Пользователь с таким E-mail уже существует!');
                    $this->respJson($sysmes, false, false);
                }
            }
            // добавлен новый t-mail
            $_SESSION['user']['email'] = $this->data['email'];
        }
        
        //$this->unsetEl('do_profile_f');

        if(!empty($this->data['email']) || isset($this->data['img'])){
            $user->update($this->data, "`login` = '".$_SESSION['user']['login']."'");
            
            $sysmes = $this->sysMessage('success','Изменения сохранены!');
            $this->respJson($sysmes, $uplfile);
        }
        $sysmes = $this->sysMessage('danger','Ошибка сохранения настроек пользователя!');
        $this->respJson($sysmes);
           
    }
    
    public function validateFiles(){

        $type = $_FILES['avatar']['type'];
        $size = $_FILES['avatar']['size'];
        $name = $_FILES['avatar']['name'];

        if(!preg_match("/\.png|jpg|jpeg|gif\$/i",$name)){
            
            $sysmes = $this->sysMessage('danger','Недопустимое расширение файла!');
            $this->respJson($sysmes);
        }
        
        if($type !== 'image/png' && $type !== 'image/jpg' && $type !== 'image/jpeg' && $type !== 'image/gif'){
            
            $sysmes = $this->sysMessage('danger','Недопустимый тип файла!');
            $this->respJson($sysmes);
        }
        if($size > Config::$size){
            
            $sysmes = $this->sysMessage('danger','Превышен допустимый размер файла!');
            $this->respJson($sysmes);
        }
        
        $extn = strrchr($name,'.');// строка с послед вхождения (расширение)
        
        $name = substr($name,0,strrpos($name,'.'));// имя без расширения (послед вхожд точки)
        
        $name = $name.'_'.$_SESSION['user']['login'].$extn;
        
        $file = "images/".strtolower($name);
        
        if(move_uploaded_file($_FILES['avatar']['tmp_name'], $file)) return strtolower($name);
        return false;
        
    }
    
    
    public function validateLogin(){// поиск пользов-ля в БД
        $view = new ViewController();
        $user = new User();
        
        if($data = $user->findLogin($this->data['login']))
            if($this->verifyUserPass($data)){
                
                $mycookie = [
                    'login'=>$_SESSION['user']['login'],
                    'ip'=>$_SESSION['user']['ip'],
                ];
                
                $this->respJson(false, false, false, $mycookie);// авторизация пройдена
                
            }
             //exit('{"redirect":"profile"}');
        
        // авторизация не пройдена 
        $type = 'danger';
        $mes = 'Авторизация не пройдена!';
                
        $sysmes = $view->prerender('message',compact('type','mes'));
            
        // сообщение о непройденной авторизации, замена крутилки
        // асинхронно вывожу сообщение
        echo json_encode(['sysmes'=>$sysmes,'submit'=>'НЕТ ДОСТУПА']);
        exit();                
        
    }
    
    public function verifyUserPass($data){
        
        // сравнить введенное пользователем и найденное в БД
        // предварительно захешировать
        
        if($this->data['password'].$data[0]->salt === $data[0]->password){

            // авторизация пройдена
            $_SESSION['user']['id'] = $data[0]->id;
            $_SESSION['user']['img'] = $data[0]->img;
            $_SESSION['user']['login'] = $data[0]->login;
            $_SESSION['user']['balance'] = $data[0]->balance;
            $_SESSION['user']['b'] = $data[0]->b;
            $_SESSION['user']['date_reg'] = $data[0]->date_reg;
            $_SESSION['user']['date_act'] = $data[0]->date_act;
            $_SESSION['user']['ip'] = $data[0]->ip;
            $_SESSION['user']['email'] = $data[0]->email;
            $_SESSION['user']['wallet'] = $data[0]->wallet;
                        
            if($this->updateUserData($data)) return true;
            
        } return false; // авторизация не пройдена
    }
    
    public function updateUserData($data){
        
        $user = new User();
        
        $id = $data[0]->id;// ID пользователя
        
        if($data[0]->n != 9) $n = $data[0]->n + 1;// кол-во посещений
        else{
            // запишу текущ дату посещения с обновлением пароля
            $ctrl = new LoginController();
                
            $newpass = $ctrl->generatePass($this->data['password'], $salt);
                
            if($user->saveDateActAndPass($id, $newpass, $salt, $n=0)) return true;
            return 'ошибка обновления даты последнего посещения';
        }
            
        // запишу текущ дату посещения 
        if($user->saveDateAct($id, $n)) return true;
        return 'ошибка обновления даты последнего посещения';
    }
    

    
    public function validateRegData(){
        $view = new Viewcontroller();
        $user = new User();
        
        if($user->validateIp($this->data)){
            
            $type = 'danger';
            $mes = 'Пользователь с вашим IP уже существует<br/>Хотите зарегистрировать второго?';
            $sysmes = $view->prerender('message',compact('type','mes'));
            
            echo json_encode(['sysmes'=>$sysmes,'btn'=>true]);
            
        }else{
            if($pos = strpos($this->data['ip'],'_0'))
                $this->data['ip'] = substr($this->data['ip'],0,$pos);
            
            if(empty($this->data['ref_id'])) $this->data['ref_id'] = 0;// значит не реферал
            
            $this->data['balance'] = 0;
            $this->data['date_reg'] = date('d-m-Y',time());
            $this->data['date_act'] = date('d-m-Y',time());
            
            $this->unsetEl('do_regist_f');

            // проверка рекапча
            $secret = '6LfvuRcUAAAAAOnEtZTBkEbVtKeqmU6vgcqIJx3a';
            //$response = $this->data['g-recaptcha-response'];// отправить POST запрос в гугл
            $response = $this->data['g-recaptcha-response'];// отправить POST запрос в гугл
            
            $this->unsetEl('g-recaptcha-response');// удалю чтобы в БД не вставлять
            
            $remoteip = $_SERVER['REMOTE_ADDR'];
            
            $obj = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response.'&remoteip='.$remoteip);// обработка гугл капчи
            
            $res = json_decode($obj,true);// верну в виде массива ответ гугла
            
            if($res['success']){
                
                $ctrl = new LoginController();// генерирую пароль
                
                $pass = $ctrl->generatePass($this->data['password'], $salt);
                
                $this->data['password'] = $pass;
                $this->data['salt'] = $salt;
                $this->data['n'] = 0; // первое посещение
                
                if($user->save($this->data)){
                
                    $type = 'success';
                    $mes = 'Поздравляем!<br/>Вы успешно зарегистрировались.';

                    $sysmes = $view->prerender('message',compact('type','mes'));
                    //echo json_encode(['sysmes'=>$sysmes]);
                    
//  ... ПРОБЛЕМА не выводится flash сообщение
//debug($sysmes);
                    // создать сообщ об успешной регистрации
                    //Session::flash('sysmes',$sysmes);
                    
                    $_SESSION['flash']['sysmes'] = $sysmes;
                    //exit();
                    
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
    
    public function validateRegLogin(){
        //$view = new Viewcontroller();
        $user = new User();
        
        if(!$user->findLogin($this->data['login'])) exit('{"icon":"ok"}');// такой логин свободен
        else{
            // иконку для очистки поля и выделение ошибки
            exit('{"icon":"remove","err":"ERR_DBL","click":"onclick=\'rem2()\'"}');
        }
    }

    public function sendEmail(){
        $view = new Viewcontroller();
        
        $title = 'Сообщение с сайта cran.local';// тема письма
        $name = $this->data['name'];
        $uemail = $this->data['email'];
        $text = nl2br($this->data['message']);
        
        $body = $view->prerender('mail',compact('title','name','uemail','text'));
        $email = Config::$admEmail;
        
        $head = 'From: admin@zolushka18.ru'."\r\n".'MIME-Version 1.0'."\r\n".'Content-type: text/html; charset=UTF-8';
        
        mail($email,$title,$body,$head);
        
        exit();
    }
    
    public function getRefList(){
        
        $user = new User();
        
        $wh = '`ref_id`='.$_SESSION['user']['id'];
        
        $asc = '`date_reg` DESC';
        
        $data = $user->find('`id`,`login`,`balance`,`date_reg`', $wh, $asc);
        
        if($data){
            
            // отформатировать дату
            
            echo json_encode(['dataRefList'=>$data]);
            exit();
        }else{
            $sysmes = $this->sysMessage('danger','Нет пользователей зарегистрированых по вашей реферальной ссылке!');
            $this->respJson($sysmes);
        }
            
            

    }
    
    public function getBList(){
        
        $his_b = new History_b();
        
        $wh = '`user_id`='.$_SESSION['user']['id'];
        
        $asc = '`date_add` DESC';
        
        $data = $his_b->find('`date_add`,`sum`', $wh, $asc);
        
        if($data){
            
            // отформатировать дату
            $data = $this->formDate($data);
            
            echo json_encode(['dataBList'=>$data]);
            exit();
        }else{
            $sysmes = $this->sysMessage('danger','У вас ещё нет полученных бонусов!');
            $this->respJson($sysmes);
        }
        
        
        
    }
    
    public function checkResponseBonus(){
        
        if(isset($_SESSION['user'])) $this->getBonus();
        else{
            $sysmes = $this->sysMessage('danger','Зарегистрируйтесь или авторизуйтесь, чтобы ежедневно получать бонусы!');
            $this->respJson($sysmes);
        }
        
    }
    
    public function getBonus(){
        
        $mod = new Bonus();
        
        $ts = time();
        $lim = strtotime('+5 minutes');// лимит времени на не получение бонуса
        $time_lim = $_SESSION['user']['time_lim'];
        
        if(empty($time_lim)) $data = $mod->find('*',"`ip` = '".$_SESSION['user']['ip']."' AND `login` = '".$_SESSION['user']['login']."'");
        
        if(isset($data) && $data != false) $time_lim = $data[0]->time_lim;
        if(isset($data) && $data == false){
            
            $mod->insert([
                'ip'=>$_SESSION['user']['ip'],
                'login'=>$_SESSION['user']['login'],
                'time_lim'=>$lim
            ]);
            
            $_SESSION['user']['time_lim'] = $lim;
            $time_lim = $ts;
        }
        
        if($ts < $time_lim){
                
            $sysmes = $this->sysMessage('danger','До получения бонуса осталось '.($time_lim - $ts).' сек.!');
            
            $mycookie = [
                'time_lim'=>$time_lim,
            ];
            
            $this->respJson($sysmes, false, false, $mycookie);
    
        }else{
                
            $mod->update([
                'time_lim'=>$lim
            ],"`ip` = '".$_SESSION['user']['ip']."' AND `login` = '".$_SESSION['user']['login']."'");
                
            $_SESSION['user']['time_lim'] = $lim;
                
            $bonus = rand(1, 100) / 100;
            
            $_SESSION['user']['balance'] += $bonus;
            $_SESSION['user']['b'] += 1;// количество бонусов
            
            $user = new User(); // обновление баланса
            $user->update([
                'balance' => $_SESSION['user']['balance'],
                'b' => $_SESSION['user']['b']
            ],"`ip` = '".$_SESSION['user']['ip']."' AND `login` = '".$_SESSION['user']['login']."'");
            
            $his_b = new History_b();// сохранение в историю
            if($his_b->insert([
                'user_id'=>$_SESSION['user']['id'],
                'date_add'=>time(),
                'sum'=>$bonus
            ])){
                
                $sysmes = $this->sysMessage('success','Поздравляем! Бонус в '.$bonus.' руб. зачислен на ваш баланс!');
            
                $mycookie = [
                    'time_lim'=>$lim,
                ];

                $this->respJson($sysmes, false, false, $mycookie);
                
            }
            $this->respJson($this->sysMessage('danger','Ошибка сохранения в БД!'), false, false, false);
            

            
        }
            
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function unsetEl($el){
        unset($this->data[$el]);
    }
    
    public function redirect($uri){
        header('Location: '.$uri);
    }
    
    public function sysMessage($type,$mes){
        $view = new ViewController();

        return $view->prerender('message',compact('type','mes')); 
    }
    
    public function respJson($sysmes=false, $flname=false, $changeEm=true, $mycookie=false){
        echo json_encode([
            'sysmes'=>$sysmes,
            'flname'=>$flname,
            'changeEm'=>$changeEm,
            'mycookie'=>$mycookie
            
        ]);
        exit();
    }
    
    public function formDate($data){
        if(is_array($data)){
            for($i=0; $i<count($data); $i++){        
                foreach($data[$i] as $k => $v){
                    if($k == 'date_add') $data[$i]->date_add = strftime('%d.%m.%Yг. %H:%M:%S',$data[$i]->date_add);
                }   
            }
        }else{
            $data = strftime('%d.%m.%Yг. %H:%M:%S',$data);
        }
        return $data;
    }
    
    
    
    public function render($tmpl,$data=[]){
        $title = $this->title;
        $meta_desc = $this->meta_desc;
        $meta_key = $this->meta_key;
        $tit = 'Пользователь '.$_SESSION['user']['login'];
        $prof = ($_SESSION['user']) ? '<li><a href="/profile" title="'.$tit.'"><i class="glyphicon glyphicon-user"></i></a></li>' : '';
        
        $left = $this->view->prerender('left');
        
        $content = $this->view->prerender($tmpl,$data);
        
        $right = $this->view->prerender('right',$this->btn);

        //$sysmes = (!empty($this->sysmes)) ? $this->sysmes : '';
        
        $sysmes = $this->sysmes;// при debug сообщение появляется

        //debug($sysmes);
        
        $this->view->render('main',compact('title','meta_desc','meta_key','prof','left','sysmes','content','right'));
        
    }

    
}








?>