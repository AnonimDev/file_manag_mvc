<?php
namespace app\controllers;

use vendor\anom\core\Controller;

class FileController extends Controller
{
    public function index(){
        Controller::rend('file');
    }


    public function ajax(){
        if (isset($_POST['dir'])){
            $dir = $_POST['dir'];

            if (empty($dir)) {
                $dir = $this->premDir();
            }

            echo $this->open($dir);
        } else echo 'Ошибка при отправке данных!!!';
    }
    private function premDir(){
        $arr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        $arr = array_shift($arr);
        //$arr .= DIRECTORY_SEPARATOR;
        return $arr;
    }
    private function not_repeat($dir){
        $arr = explode(DIRECTORY_SEPARATOR, $dir);
        array_pop($arr);
        $arr = implode(DIRECTORY_SEPARATOR, $arr);
        return $arr;
    }
    private function filesize_format($filesize){
        $formats = array(' Б',' КБ',' МБ',' ГБ',' ТБ');// варианты размера файла
        $format = 0;// формат размера по-умолчанию

        // прогоняем цикл
        while ($filesize > 1024 && count($formats) != ++$format)
        {
            $filesize = round($filesize / 1024, 2);
        }
        $formats[] = ' ТБ';

        return $filesize.$formats[$format];
    }
    private function openDir($dir){
        $dirs = array();
        $files = array();
        $path = trim($dir, '/');
        if (!is_file($dir)) {
            if ($dir = @opendir($path)) {
                while (false !== ($file = readdir($dir))) {
                    if ($file == '.' || $file == '..') continue;

                    if (is_file($path . DIRECTORY_SEPARATOR . $file)) {
                        $files[] = $path . DIRECTORY_SEPARATOR . $file;
                    } else {
                        $dirs[] = $path . DIRECTORY_SEPARATOR . $file;
                    }
                    sort($dirs);
                    sort($files);
                }

                closedir($dir);
                $rezult[] = $dirs;
                $rezult[] = $files;
                return $rezult;
            } else return 101;
        } else {
            $ext = pathinfo($dir, PATHINFO_EXTENSION);
            if ( $ext == 'txt' ){
                return 'txt';
            } else return 100;
        }
    }

    private function forMass($arr, $type){
        $arr2 = [];
        $size = 0;
        foreach ($arr as $key => $value) {
            $label = mb_strtolower(mb_substr(mb_strrchr($arr[$key], DIRECTORY_SEPARATOR), 1));
            if ($type == 'Файл'){
                $size = $this->filesize_format(filesize($arr[$key]));
            }
            $arr2[$arr[$key]] = [
                'label' => $label,
                'size' => $size,
                'type' => $type
            ];
        }
        return $arr2;
    }

    private function open($dir){
        $rezult = $this->openDir($dir);

        if (!empty($rezult)){
            $array = [
                'status' => '',
                'dir' => $dir,
                'prev' => $this->not_repeat($dir),
                'files' => '',
                'dirs' => '',
                'text' => '',
                'separator' => DIRECTORY_SEPARATOR
            ];
        }

        if (is_array($rezult)) {
            $array['status'] = 200;
            $array['files'] = $this->forMass($rezult[1], 'Файл');
            $array['dirs'] = $this->forMass($rezult[0], 'Папка');
            //$rez = json_encode($array);
        }
        if ($rezult === 'txt'){
            $fd = file_get_contents($dir);
            $fd = iconv('windows-1251', 'utf-8', $fd);
            $fd = nl2br($fd);
            $array['status'] = 300;
            $array['text'] = $fd;
        }
        if ($rezult === 100){
            $array['status'] = 400;
            $array['text'] = 'Такие файлы я пока не умею открывать';
        }
        if ($rezult === 101){
            $array['status'] = 400;
            $array['text'] = 'Не могу открыть директорию';
        }

        return json_encode($array);
    }

}