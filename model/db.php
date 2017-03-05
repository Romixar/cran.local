<?php

class DB{
    
    public $dbh; // соединение с БД
    
    public function __construct(){
        
        $dbh = new PDO('mysql:dbname='.Config::$dbname.';host='.Config::$dbhost, Config::$dbuser, Config::$dbpass);
        
        if(!isset($dbh)) echo 'Ошибка соединения с базой даннных';
        else $this->dbh = $dbh;
        
    }
    
    public function select($sql){
        
        $sth = $this->dbh->query($sql);
        return $sth->fetchAll(PDO::FETCH_CLASS, 'user');// возвр объект указанного класса
        
        
        
    }
    
    
    public function validateIp($data){
        $sql = "SELECT * FROM `users` WHERE `ip` = '".$data['ip']."'";
        
        $res = $this->select($sql);
        
        if(!empty($res)) return true;
        return false;
    }
    
    
    public function findUser($data){
        
        $sql = "SELECT * FROM `users` WHERE `login` = '".$data['login']."' AND `password` = '".$data['password']."'";
        
        $res = $this->select($sql);
        
        
        if(!empty($res) && count($res) == 1){
            
            $_SESSION['user']['login'] = $res[0]->login;
            $_SESSION['user']['balance'] = $res[0]->balance;
            $_SESSION['user']['date_reg'] = $res[0]->date_reg;
            $_SESSION['user']['date_act'] = $res[0]->date_act;
            $_SESSION['user']['ip'] = $res[0]->ip;
            
            // запишу текущ дату посещения
            if($this->saveDateAct($data['login'],$data['password'])) return true;
            return 'ошибка обновления даты последнего посещения';
            
            
        }else return false;

        
        
    }
    
    public function saveDateAct($login,$pass){
        
        $sql = "UPDATE `users` SET `date_act` = '".date('d-m-Y',time())."' WHERE `login` = '".$login."' AND `password` = '".$pass."'";
        
        $sth = $this->dbh->query($sql);
        return $sth->rowCount();
    }
    
    public function save($data){
        
        $sql = "INSERT INTO `users` VALUES('','".$data['login']."','".$data['password']."','".$data['ip']."',".$data['balance'].",'".$data['date_reg']."','".$data['date_act']."')";
        
        $sth = $this->dbh->query($sql);
        
        // кол-во модифицир-х строк
        if($sth->rowCount()){
            $_SESSION['user']['login'] = $data['login'];
            $_SESSION['user']['balance'] = $data['balance'];
            //$_SESSION['user']['date_reg'] = $res[0]->date_reg;
            
            
            return true;
        }
        return false;
        
    }
    
    
    
    
    
    
    
    
}




?>