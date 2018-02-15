<?php
use app\test;
use vendor\rr;
use vendor\anom\core\Router;

require '../vendor/anom/core/Loader.php';
$loader = new Loader();
spl_autoload_register([$loader, 'LoadClass']);

$router = new Router();
$router->start();