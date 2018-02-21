<?php
namespace app\controllers;

use vendor\anom\core\Controller;

class ConsoleController extends Controller
{
    private $array = [
        'status' => '',
        'text' => ''
    ];

    public function index(){
        Controller::rend('console');
    }
    public function ajaxConsole(){
        //echo 123;
        //var_dump($this->array);
        if (isset($_POST['comand'])){
            $comand = $_POST['comand'];
            $this->array['status'] = '200';

            echo $this->comand($comand);
        } else echo 'Ошибка при отправке данных!!!';
    }
    private function comand($comand){
        //exec($comand, $out);
        $this->array['text'] = system($comand);

        return json_encode($this->array);
    }

}