<?php

class LoginController{
    
    
    public static function auth(){
        echo 'попал на страницу авторизации!';
        exit();
    }
    
    
    
    
    public function generatePass($pass, &$salt){
        
//        $str = Config::$secret_str;
//        $max = strlen($str);
//
//        $salt = '';
//        for($i=0; $i<Config::$len; $i++){
//            
//            $pos = rand(1, $max);
//            
//            $salt .= substr($str,$pos,1);// выбираю по одному символу            
//        }
        
        $salt = $this->randStr();
        
        // сделать  хэш

        return $pass.$salt;
//        return $salt;
//        exit();

    }
    
    public function randStr(){
        
        $str = Config::$secret_str;
        $max = strlen($str);

        $rstr = '';
        for($i=0; $i<Config::$len; $i++){
            
            $pos = rand(1, $max);
            $rstr .= substr($str,$pos,1);// выбираю по одному символу            
        }
        return $rstr;
    }
    
    
    
    
}






?>