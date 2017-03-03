<?php

class DB{
    
    public $dbh; // соединение с БД
    
    public function __construct(){
        
        $dbh = new PDO('mysql:dbname='.Config::$dbname.';host='.Config::$dbhost, Config::$dbuser, Config::$dbpass);
        
        if(!isset($dbh)) echo 'Ошибка соединения с базой даннных';
        else $this->dbh = $dbh;
        
    }
    
    public function execute($sql){
        
        $sth = $this->dbh->query($sql);
        return $sth->fetchAll(PDO::FETCH_CLASS, 'user');// возвр объект указанного класса
        
        
        
    }
    
    
    
    public function findUser($data){
        
        //$stmt = $this->dbh->query("SELECT * FROM `users` WHERE `login` = 'romario'", PDO::FETCH_ASSOC);
        $sql = "SELECT * FROM `users` WHERE `login` = '".$data['login']."' AND `password` = '".$data['password']."'";
        
        $res = $this->execute($sql);
        
        
        if(empty($res)) echo 'такого пользователя не существует';
        //else{
            
            
            //$_SESSION['user']['login'] = $res[0]->login;
            
        //}
        
        
    }
    
    
    
    
    
    
    
    
}




?>