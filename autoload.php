<?php

function __autoload($class_name){
    
    $file = $class_name.'.php';
    
    $path = [
        '','\auth','\controller','\view','\model','\auth\model','\auth\controller','\auth\view'
    ];
    for($i=0; $i<count($path); $i++){
        if(file_exists(__DIR__.$path[$i].'\\'.$file)) require_once __DIR__.$path[$i].'\\'.$file;
    }
}

function debug($data){
    echo '<pre>'.print_r($data,true).'</pre>';
}

?>