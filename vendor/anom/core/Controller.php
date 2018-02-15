<?php

namespace vendor\anom\core;

class Controller {
    private static $view = '../app/views/';

    static public function rend($cont){
        $pref = debug_backtrace()[0]['file'];
        $pref = explode(DIRECTORY_SEPARATOR, $pref);
        $pref = array_pop($pref);
        $pref = substr($pref, 0, 4);
        $pref = $pref . '/';
        include (self::$view . 'layout/layout.php');
        $content = include (self::$view . $pref . $cont . '.php');
    }
}