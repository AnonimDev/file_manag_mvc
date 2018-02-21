<?php

namespace vendor\anom\core;

class Controller {
    private static $view = '../app/views/';

    static public function rend($cont){
        $pref = debug_backtrace()[0]['file'];
        $pref = explode(DIRECTORY_SEPARATOR, $pref);
        $pref = array_pop($pref);
        $pref = substr($pref, 0, -14);
        $pref = $pref . '/';
        $a = file_get_contents(self::$view . $pref . $cont . '.php');
        $b = file_get_contents(self::$view . 'layout/layout.php');
        $content = str_replace('<!--[CONTENT]-->', $a, $b);
        $temp = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($temp, $content);
        include($temp);
        unlink($temp);
    }
}