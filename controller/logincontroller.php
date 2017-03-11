<?php


class LoginController{
    
    

    
    

    
    
    
    
    public function generatePass($pass, &$salt){

        
        $salt = $this->randStr();
        
        // сделать  хэш

        return $pass.$salt;

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