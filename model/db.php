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
        return $sth->fetchAll(PDO::FETCH_CLASS, get_called_class());// возвр объект указанного класса
        
        //return $sth->fetchAll();// возвр массив
        
    }
    
    
    
    
    
    
    
    public function cntRow($f){
        
        $sql = "SELECT COUNT(`".$f."`) FROM `".static::$table."`";
        
        $sth = $this->dbh->query($sql);
        $res = $sth->fetchAll();
        
        
        if(!empty($res) && count($res) == 1) return $res[0][0];
        return false;
        
        
    }
    
    
    public function validateIp($data){
        $sql = "SELECT `id` FROM `users` WHERE `ip` = '".ip2long($data['ip'])."'";
        
        $res = $this->select($sql);
        
        if(!empty($res)) return true;
        return false;
    }
    
    
//    'SELECT `title`,`n`,`v`,`history_s`.`serf_ids`
//    
//    FROM `serfing` JOIN `history_s`
//
//    WHERE `history_s`.`user_id` = 4
//                
//    AND `history_s`.`date_add` BETWEEN 1489730699 AND 1489780699'
        
    public function findSerfLinks($fields, $user_id, $yes_ts, $tod_ts, $mon_ts){
        
        $sql = 'SELECT '.$fields.'
    
                FROM `'.static::$table.'` JOIN `history_s`

                WHERE `history_s`.`user_id` = '.$user_id.'
                
                AND `history_s`.`date_add` BETWEEN '.$yes_ts.' AND '.$tod_ts.'
                AND `serfing`.`date_add` > '.$mon_ts;
        
    
        //echo $sql; //die;
        
        $res = $this->select($sql);
        if(!empty($res)) return $res;
        return false;
    }
    
    
    public function findSerfData($fields, $serf_id, $user_id, $yes_ts, $tod_ts){
        
        $sql = 'SELECT '.$fields.'
                FROM `'.static::$table.'`
                JOIN `serfing`
                WHERE `serfing`.`id` = '.$serf_id.'
                AND `history_s`.`user_id` = '.$user_id.'
                AND `history_s`.`date_add` BETWEEN '.$yes_ts.' AND '.$tod_ts;
        
            
    //  1492722000 - полночь 20.04
    
    //  1492808400 - полночь 21.04
        
        
    //echo $sql; //die;
        $res = $this->select($sql);
        if(!empty($res)) return $res;
        return false;
    }
    
    public function findStaticLinkData($f, $link_id, $user_id, $yes_ts, $week_ts){
        
        $sql = 'SELECT '.$f.'
                FROM `'.static::$table.'`
                JOIN `contextlinks`
                WHERE `contextlinks`.`id` = '.$link_id.'
                AND `history_st`.`user_id` = '.$user_id.'
                AND `history_st`.`date_add` BETWEEN '.$yes_ts.' AND '.$week_ts;
        
        //echo $sql;die;
        
        $res = $this->select($sql);
        if(!empty($res)) return $res;
        return false;
    }
    
    public function findCntxtLinkData($f, $link_id, $user_id, $yes_ts, $week_ts){
        
        $sql = 'SELECT '.$f.'
                FROM `'.static::$table.'`
                JOIN `contextlinks`
                WHERE `contextlinks`.`id` = '.$link_id.'
                AND `history_c`.`user_id` = '.$user_id.'
                AND `history_c`.`date_add` BETWEEN '.$yes_ts.' AND '.$week_ts.'
                AND `contextlinks`.`period` = 0';
        
        //echo $sql;//die;
        
        $res = $this->select($sql);
        if(!empty($res)) return $res;
        return false;
        
        
        
    }
    
    public function findEmail($em){
        
        $sql = "SELECT * FROM `users` WHERE `email` = '".$em."'";
        
        $res = $this->select($sql);
        
        if(!empty($res)) return $res;
        return false;
        
        
    }
    
    public function findImg($img){
        $sql = "SELECT * FROM `users` WHERE `img` = '".$img."'";
        
        $res = $this->select($sql);
        
        if(!empty($res)) return $res;
        return false;
        
    }
    
    public function findOnLogin($lg){
        $sql = "SELECT * FROM `".static::$table."` WHERE `login` = '".$lg."'";
        
        $res = $this->select($sql);
        
        if(!empty($res) && count($res) == 1) return $res;
        return false;
    }
    
    public function find($fields, $where='', $asc='',$lim=''){
        
//        if(strpos($fields,'`') === false){
//            
//            $arr = explode(',',$fields);
//            
//            for($i=0; $i<count($arr); $i++) $arr[$i] = '`'.$arr[$i].'`';
//            
//            $fields = implode(',',$arr);
//        }
        
        $sql = "SELECT ".$fields." FROM `".static::$table."`";
        
        if(!empty($where)) $sql .= ' WHERE '.$where;
        if(!empty($asc)) $sql .= ' ORDER BY '.$asc;
        if(!empty($lim)) $sql .= ' LIMIT '.$lim;
        
        //debug($sql);//die;

        $res = $this->select($sql);
        if(!empty($res)) return $res;
        return false;
        
        
        
    }
    
    public function getArr($sql){ // извлечение ввиде массива
        
        $sth = $this->dbh->query($sql);
        return $sth->fetchAll();
    }
    

    
    public function findComments($fields){
        $sql = "SELECT ".$fields." FROM `".static::$table."` ORDER BY `date_add` DESC";
        $sth = $this->dbh->query($sql);
        $res = $sth->fetchAll();
        
        if(!empty($res)) return $res;
        return false;
        
    }
    

    
    public function update($fields, $where=''){
        
        if(is_array($fields)){
            foreach($fields as $k => $v) $data[] = "`".$k."` = '".$v."'";
        }else return false;
        
        $sql = 'UPDATE `'.static::$table.'` SET '.implode(',',$data);
        
        //$sql = 'INSERT INTO tbl_name ('.implode(',',$keys).') VALUES ('.implode(',',$vals).')';
        
        if(!empty($where)) $sql .= ' WHERE '.$where;

        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
    }
    
    public function updateViewSerf($serf_id){
        
        $sql = 'INSERT INTO `'.static::$table.'` ( `id`, `v` )  VALUES('.$serf_id.',1) ON DUPLICATE KEY UPDATE `v` = `v` + VALUES(`v`)';
        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
    }
    public function setSerfZero($tmp){// установить 0 просмотров если прошли сутки
        
        foreach($tmp as $k => $v) $str .= '('.$k.','.$v.',0),';
        
        $str = substr($str,0,-1);
        
        $sql = 'INSERT INTO `'.static::$table.'` (`id`,`k`,`v`) VALUES '.$str.' ON DUPLICATE KEY UPDATE `k` = VALUES(`k`), `v` = VALUES(`v`)';
        
        echo $sql;//die;
        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
    }
    
    public function updateRefUsers($ids){
        
        for($i=0; $i<count($ids); $i++) $str .= '('.$ids[$i].',0,0,0),';
        
        $str = substr($str,0,-1);
        
        $sql = 'INSERT INTO `'.static::$table.'` (`id`,`ref_id`,`date_ref`,`ref_b`) VALUES '.$str.' ON DUPLICATE KEY UPDATE `ref_id` = VALUES(`ref_id`), `date_ref` = VALUES(`date_ref`), `ref_b` = VALUES(`ref_b`)';
        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
    }
    
    public function update2Balances($data){ // обновить баланс реферала и реферера

        $keys = array_keys($data);
        $vals = array_values($data);
        
        $sql = 'INSERT INTO `'.static::$table.'` ( `id`,  `balance` ,  `b` )  VALUES('.$keys[0].','.$vals[0][0].','.$vals[0][1].'),('.$keys[1].','.$vals[1][0].','.$vals[1][1].') ON DUPLICATE KEY UPDATE `balance` = `balance` + VALUES(`balance`), `b` = `b` + VALUES(`b`)';
        
        //'insert into `users` ( `id`,  `balance` ,  `b` )  values(1,200,0),(4,150,1) on duplicate key update `balance` = `balance` + values(`balance`), `b` = `b` + values(`b`)';

//        'UPDATE `'.static::$table.'` SET
//                `balance` = CASE
//                WHEN `id` = 4 THEN `balance` + 50
//                WHEN `id` = 45 THEN `balance` + 100 END
//                WHERE `id` IN (4, 45)';
        
        
        
        
        

           //echo $sql;die;    
        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
        
    }
    
    public function update3Rows($data){
        
//        for($i=0; $i<count($data); $i++){
//                
//            $str .= '('.$data[$i]['id'].','.$data[$i]['balance'].','.$data[$i]['ref_id'].','.$data[$i]['date_ref'].','.$data[$i]['t_ref'].','.$data[$i]['ref_b'].'),';
//                
//        }
//        $str = substr($str,0,-1);
        

        $sql = 'UPDATE `'.static::$table.'` SET
        
       `balance` = CASE
       
       WHEN `id` = '.$data[0]['id'].' THEN '.$data[0]['balance'].'
       WHEN `id` = '.$data[1]['id'].' THEN `balance`
       WHEN `id` = '.$data[2]['id'].' THEN `balance` + '.$data[2]['balance'].'
       
       END,
       
       `ref_id` = CASE
       
       WHEN `id` = '.$data[0]['id'].' THEN `ref_id`
       WHEN `id` = '.$data[1]['id'].' THEN '.$data[1]['ref_id'].'
       WHEN `id` = '.$data[2]['id'].' THEN `ref_id`
       
       END,
       
       `date_ref` = CASE
       
       WHEN `id` = '.$data[0]['id'].' THEN `date_ref`
       WHEN `id` = '.$data[1]['id'].' THEN '.$data[1]['date_ref'].'
       WHEN `id` = '.$data[2]['id'].' THEN `date_ref`
       
       END,
       
       `t_ref` = CASE
       
       WHEN `id` = '.$data[0]['id'].' THEN `t_ref` + 1
       WHEN `id` = '.$data[1]['id'].' THEN `t_ref`
       WHEN `id` = '.$data[2]['id'].' THEN `t_ref` - 1
       
       END,
       
       `ref_b` = CASE
       
       WHEN `id` = '.$data[0]['id'].' THEN `ref_b`
       WHEN `id` = '.$data[1]['id'].' THEN '.$data[1]['ref_b'].'
       WHEN `id` = '.$data[2]['id'].' THEN `ref_b`
       
       END
       
       WHERE `id` IN ('.$data[0]['id'].', '.$data[1]['id'].', '.$data[2]['id'].')';
        
        
        
        
        
        
        
//        $sql = 'INSERT INTO `'.static::$table.'` (`id`,`balance`,`ref_id`,`date_ref`,`t_ref`,`ref_b`) VALUES'.$str.' ON DUPLICATE KEY UPDATE `balance` = `balance` + VALUES(`balance`), `ref_id` = `ref_id` + VALUES(`ref_id`), `date_ref` = `date_ref` + VALUES(`date_ref`), `t_ref` = `t_ref` + VALUES(`t_ref`), `ref_b` = `ref_b` + VALUES(`ref_b`)';
        
        
        $sth = $this->dbh->query($sql);
        
        return $sth->rowCount();
        
        
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
    
    public function insert($data){// вставка по одной строке либо несколько
        $keys = [];
        $vals = [];
        
        foreach($data as $k => $v){
            
            if(strpos($k,',') !== false) $strkeys = '('.$k.')';
            else $keys[] = '`'.$k.'`';

            if(strpos($v,'(') !== false) $strvals = $v;
            else{
                if(is_numeric($v)) $vals[] = $v;
                else $vals[] = "'".$v."'";
                $fl = 1;
            }
            
        }
        
        if(!isset($strvals)) $strvals = '('.implode(',',$vals).')';
        if(!isset($strkeys)) $strkeys = '('.implode(',',$keys).')';
        
        $sql = 'INSERT INTO `'.static::$table.'` '.$strkeys.' VALUES '.$strvals;
        
        //echo $sql;exit;
        
        $sth = $this->dbh->query($sql);
        
        if($fl) return $this->dbh->lastInsertId();
        else return $sth->rowCount();
        
        //return $this->dbh->lastInsertId();
    }
    
    public function save($data){
        
        $id = $this->insert($data);

        if($id){
            session_start();
            
            $_SESSION['user']['login'] = $data['login']; // обновление сессии либо создание новой
            $_SESSION['user']['balance'] = $data['balance'];
            $_SESSION['user']['date_reg'] = $data['date_reg'];
            $_SESSION['user']['date_act'] = $data['date_act'];
            $_SESSION['user']['wallet'] = $data['wallet'];
            $_SESSION['user']['ip'] = $data['ip'];
            $_SESSION['user']['id'] = $id;
            
            
            
            return true;
        }
        return false;
        
    }
    
    public function getBonusRating(){
        
        $sql = 'SELECT `login`,`user_id`,max(`date_add`),`b`,count(`sum`),sum(`sum`) FROM `'.static::$table.'` JOIN `users` WHERE `users`.`id` = `history_b`.`user_id` GROUP BY `user_id` ORDER BY sum(`sum`) DESC';
        
        $sth = $this->dbh->query($sql);
        $res = $sth->fetchAll();
        
        if(!empty($res)) return $res;
        return false;
        
    }
    
    public function getRefPageData($lim=''){
        
//        $sql = 'SELECT `login`,`img`,`date_add`,`user_id` FROM `'.static::$table.'` JOIN `ref_page` WHERE `users`.`id` = `ref_page`.`user_id` ORDER BY `date_add` DESC';    
        
        $sql = 'SELECT `ref_page`.`id`,`login`,`img`,`date_add`,`user_id` FROM `'.static::$table.'` JOIN `ref_page` WHERE `users`.`id` = `ref_page`.`user_id` ORDER BY `date_add` DESC';
        
        if($lim) $sql .= ' LIMIT '.$lim;
        
        $sth = $this->dbh->query($sql);
        
        $res = $this->removeDoubleKeys($sth->fetchAll());
        
        if(!empty($res)) return $res;
        return false;
    }
    
    public function getRefStock(){
        
        $sql = 'SELECT `users`.`id`,`login`,`t_ref`,`rating`,`date_reg`,`date_ref`,`date_act`, `refstock`.`user_id`,`refstock`.`seller_id`,`refstock`.`seller`,`refstock`.`price` FROM `'.static::$table.'` JOIN `refstock` WHERE `users`.`id` = `refstock`.`user_id` AND `refstock`.`buy` = 0 ORDER BY `refstock`.`date_add` DESC';
        
        $sth = $this->dbh->query($sql);
        
        $res = $this->removeDoubleKeys($sth->fetchAll());
        
        if(!empty($res)) return $res;
        return false;
    }
    
    private function removeDoubleKeys($data){// удаление дублирующих числовых ключей
        
        for($i=0; $i<count($data); $i++){
            
            foreach($data[$i] as $k => $v) if(is_numeric($k)) unset($data[$i][$k]);
        }
        return $data;
    }
    
    
    public function delete($w){
        
        $sql = 'DELETE FROM `'.static::$table.'` WHERE '.$w;
        
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        
        if($sth->rowCount()) return true;
        else return false;
    }
    
//    'SELECT `id`,`login`,`ref_id` FROM `users` WHERE `ref_id`=4' - ' рефералы 4-го'
//        
//        
//        
//        
//    'SELECT `id`,`ref_id`, COUNT(`ref_id`) FROM `users` WHERE `id`=4 GROUP BY `ref_id`' - 'кол-во рефералов'
//        
//    
//    'SELECT `id`,`login`,COUNT(DISTINCT `ref_id`) FROM `users` GROUP BY `id` ORDER BY 3 DESC'
//        
//        
//        
//    'SELECT `id`,`login` FROM `users` WHERE `ref_id` IN (SELECT `id` FROM `users` WHERE `ref_id`=4)'
//        
//        
//        
//        
//    'SELECT `id`,`login`,COUNT(`id`) FROM `users` WHERE `id` IN (SELECT `ref_id` FROM `users` WHERE `ref_id` IN (SELECT `id` FROM `users` WHERE `ref_id`=4)) GROUP BY `ref_id`  ' - 'тот реферал у которго есть рефералы'
//        
    
        
    
    
}




?>