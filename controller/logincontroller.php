<?php


class LoginController{
    
    

    
    

    
    
    
    
    public function generatePass($pass, &$salt){

        
        $salt = $this->randStr(33,126);
        
        
        
        // сделать  хэш

        return $pass.Config::$loc_salt.$salt;

    }
    
    public function randStr($min,$max,$length=''){
        
        for($i=0; $i<Config::$len; $i++) $rstr .= chr(rand($min,$max));// возвращ по одному символу
        
        $this->deleteChr($rstr);
        
        //if(!$length) return $rstr;
    }
    
    public function deleteChr($str){
        
        $chr = ['`','"',"'"];// удаляю ненужные символы
        $str = str_replace($chr, '', $str);
        
        $len = strlen($str);
        
        if($len !== 8) $this->randStr(33,126,$len);
        else return $str;
        
        
//        while($len === 8){
//            
//            $this->randStr(33,126,$len);
//        }do return $str;
            
        
    }
    
    
    
    
}






?>