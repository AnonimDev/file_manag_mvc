<?php
namespace vendor\anom\core;

class Router{
    public function start(){
        $route = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $routing = [
            "/" => ['controller' => 'File', 'action' => 'index'],
            "/console" => ['controller' => 'Console', 'action' => 'index'],
            "/ajax" => ['controller' => 'File', 'action' => 'ajax'],
            "/ajaxConsole" => ['controller' => 'Console', 'action' => 'ajaxConsole']
        ];
        $assets = [
          "js" => [
              '/js/jquery.cookie.js' => 'js/jquery.cookie.js',
              '/js/main.js' => 'js/main.js',
              '/js/console.js' => 'js/console.js'
          ],
          "css" => [
              '/css/style.css' => 'css/style.css'
          ]
        ];

        if(isset($routing[$route])){
            $controller = 'app\\controllers\\' . $routing[$route]['controller'] . 'Controller';
            $controller_o = new $controller;
            $controller_o->{$routing[$route]['action']}();
        } else if(isset($assets['js'][$route])){
            include $assets['js'][$route];
        } else if(isset($assets['css'][$route])){
            include $assets['css'][$route];
        } else{
            echo 'Ошибка 404. Файл ' . $route . ' не найден!';
        }
    }
}