<?php


class LoginController{
    
    

    
    

    
    
    
    
    public function generatePass($pass, &$salt){

        
        $salt = $this->randStr();
        
        // сделать  хэш

        return $pass.Config::$loc_salt.$salt;

    }
    
    public function randStr(){
        
        for($i=0; $i<Config::$len; $i++){
            
            $rstr .= chr(rand(33, 126));// возвращ по одному символу
            
        }
        return $rstr;
    }
    
    
    
    
}






?>