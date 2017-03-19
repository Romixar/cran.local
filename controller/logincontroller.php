<?php


class LoginController{
    
    

    
    

    
    
    
    
    public function generatePass($pass, &$salt){

        
        $salt = $this->randStr(33,126);
        
        // сделать  хэш

        return $pass.Config::$loc_salt.$salt;

    }
    
    public function randStr($min,$max){
        
        for($i=0; $i<Config::$len; $i++) $rstr .= chr(rand($min,$max));// возвращ по одному символу
        return $rstr;
    }
    
    
    
    
}






?>