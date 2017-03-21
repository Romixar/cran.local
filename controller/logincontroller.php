<?php


class LoginController{
    
    

    
    

    
    
    
    
    public function generatePass($pass, &$salt){

        
        $salt = $this->randStr(33,126);
        
        
        
        // сделать  хэш

        return $pass.Config::$loc_salt.$salt;

    }
    
    public function randStr($min,$max){
        
        // возвращ по одному символу
        for($i=0; $i<Config::$len; $i++) $rstr .= chr($this->randWithout($min,$max));
        return $rstr;
    }
    
    public function randWithout($min,$max){
        $n = 100;
        do $n = rand($min,$max);
        while($n === 96 || $n === 34 || $n === 39);// если кавычки, то снова рандом
        return $n;
    }
    
    
    
    
}






?>