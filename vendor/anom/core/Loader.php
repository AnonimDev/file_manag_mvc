<?php

class Loader{
    public function LoadClass($class){
        $arr = explode('\\', $class);

        $end = array_pop($arr);
        $cout = count($arr);

        for ($i = 1; $i <= $cout; $i++) {
            //echo $i;
            if ($i == 1){
                $prefix = '../' . array_shift($arr) . '/';
            } else{
                $prefix .= array_shift($arr) . '/';
            }
        }

        $file = $prefix . $end . '.php';
        if(is_file($file)){
            require_once $file;
        }
    }
}