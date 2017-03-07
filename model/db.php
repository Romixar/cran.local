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
    
    public function findLogin($lg){
        $sql = "SELECT * FROM `users` WHERE `login` = '".$lg."'";
        
        $res = $this->select($sql);
        
        if(!empty($res) && count($res) == 1) return $res;
        return false;
    }

    
    public function saveDateAct($id, $n){
        
        $sql = "UPDATE `users` SET `n` = ".$n.", `date_act` = '".date('d-m-Y',time())."' WHERE `id` = ".$id;
        
        $sth = $this->dbh->query($sql);
        return $sth->rowCount();
    }
    
    public function saveDateActAndPass($id, $newpass, $salt, $n){
        
        $sql = "UPDATE `users` SET `password` = '".$newpass."', `salt` = '".$salt."', `n` = ".$n.", `date_act` = '".date('d-m-Y',time())."' WHERE `id` = ".$id;
        
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
            $_SESSION['user']['date_reg'] = $data['date_reg'];
            $_SESSION['user']['date_act'] = $data['date_act'];
            $_SESSION['user']['ip'] = $data['ip'];
            
            
            return true;
        }
        return false;
        
    }
    
    
    
    
    
    
    
    
}




?>