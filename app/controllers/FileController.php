<?php
namespace app\controllers;

use vendor\anom\core\Controller;

class FileController extends Controller
{
    private $array = [
        'status' => 'action',
        'dir' => '',
        'text' => ''
    ];

    public function index(){
        Controller::rend('file');
    }

    public function ajax(){
        if (isset($_POST['dir']) && isset($_POST['action']) && isset($_POST['name'])){
            $dir = $_POST['dir'];
            $action = $_POST['action'];
            $name = $_POST['name'];
            if (empty($action)) {
                if (empty($dir)) {
                    $dir = $this->allDir();
                }
                echo $this->open($dir);
            } else {
                if (!empty($dir) && !empty($action)) {
                    echo $this->actionParse($dir, $action, $name);
                } else echo 'Ошибка!!! Пустой action.';
            }
        } else if (isset($_GET['download'])){
            $file = $_GET['download'];
            $this->doloadFile($file);
        }
        else echo 'Ошибка при отправке данных!!!';
    }
    private function open($dir){
        $i = 0;
        if (is_array($dir)){
            while(count($dir) > $i){ /* Проходимся по массиву */
                $dirs[] = $dir[$i];
                $i++;
            }
            $rezult[0] = $dirs;
        } else $rezult = $this->openDir($dir);

        if (!empty($rezult)){
            $array = [
                'status' => '',
                'dir' => '',
                'files' => '',
                'dirs' => '',
                'prev' => '',
                'text' => '',
                'separator' => DIRECTORY_SEPARATOR
            ];

            if (is_array($dir)){
                $array['status'] = 200;
                $array['dirs'] = $this->forMass($rezult[0], 'Папка', false);
                $array['dir'] = '';
                //$array['prev'] = $this->not_repeat($dir);
            } else {

                $array['dir'] = $dir;
                $array['prev'] = $this->not_repeat($dir);

                if (is_array($rezult)) {
                    $array['status'] = 200;
                    $array['files'] = $this->forMass($rezult[1], 'Файл', true);
                    $array['dirs'] = $this->forMass($rezult[0], 'Папка', true);
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
            }
            return json_encode($array);
        }
    }
    private function openDir($dir){
        //$rezult = array();
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
            if ($ext == 'txt' || $ext == 'ini') {
                return 'txt';
            } else return 100;
        }
    }
    private function forMass($arr, $type, $stat){
        $arr2 = [];
        $size = 0;
        //$pers = '';
        //$dateFile = '';
        foreach ($arr as $key => $value) {
            $label = ($stat) ? mb_strtolower(mb_substr(mb_strrchr($arr[$key], DIRECTORY_SEPARATOR), 1)) : $arr[$key];
            $pers = $this->perssionFile($arr[$key]);
            $dateFile = $this->dateFile($arr[$key]);
            //$label = mb_strtolower(mb_substr(mb_strrchr($arr[$key], DIRECTORY_SEPARATOR), 1));
            //$label = $arr[$key];

            if ($type == 'Файл'){
                $size = $this->filesize_format(filesize($arr[$key]));
            }
            $arr2[$arr[$key]] = [
                'label' => $label,
                'size' => $size,
                'date' => $dateFile,
                'perssion' => $pers,
                'type' => $type
            ];
        }
        return $arr2;
    }
    private function not_repeat($dir){
        $arr = explode(DIRECTORY_SEPARATOR, $dir);
        array_pop($arr);
        $arr = implode(DIRECTORY_SEPARATOR, $arr);
        return $arr;
    }
    private function allDir(){
        $sensitivity = 1073741824;
        //$sensitivity = 64424509440;
        $result = array();
        $char = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','w','x','y','z');
        $i = 0;
        $k = 0;
        ini_set('display_errors', 'Off'); /* Делаем так, потому что при определени приводов, кардридеров и прочьего будут выкидоваться ошибки */
        while(count($char) > $i){ /* Проходимся по массиву */
            $path = $char[$i] . ":"; /* Определяем в строке путь */
            if(is_dir($path)){ /* Проверяем есть ли такой каталог в системе */
                $spaces = disk_total_space($char[$i] . ':') + disk_free_space($char[$i] . ':'); /*Считаем общее кол-во памяти на нем */
                if($spaces > $sensitivity){ /* Проверяем на кол-во памяти, чтобы отбросить диски, кард риадеры... */
                    $result[$k++] = $path; /* Записываем в массив путь */
                }
            }
            $i++;
        }
        ini_set('display_errors', 'On');
        return $result;
    }
    private function premDir(){
        //Для linux-систем(в разработке)
        $arr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        $arr = array_shift($arr);
        //$arr = $this->allDir();
        //$arr .= DIRECTORY_SEPARATOR;
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
    private function dateFile($filename){
       return date("d.m.Y H:i:s", @filectime($filename));
    }
    private function perssionFile($perms){
        $perms = @fileperms($perms);

        if (($perms & 0xC000) == 0xC000) {
            // Сокет
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            // Символическая ссылка
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            // Обычный
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            // Специальный блок
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            // Директория
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            // Специальный символ
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            // Поток FIFO
            $info = 'p';
        } else {
            // Неизвестный
            $info = 'u';
        }

        // Владелец
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

        // Группа
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

        // Мир
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    private function actionParse($dir, $action, $name){


        switch ($action){
            case 'doload':
                $this->doloadFile($dir);
                break;
            case 'copy':
                $this->copyFile($dir, $name);
                break;
            case 'move':
                $this->moveFile($dir, $name);
                break;
            case 'remove':
                $this->removeFile($dir);
                break;
            case 'newfolder':
                $this->newfolder($dir, $name);
                break;
            case 'zipARX':
                $this->zipARX($dir);
                break;
          }

          return json_encode($this->array);
    }
    private function doloadFile($file){

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        // читаем файл и отправляем его пользователю
        if ($fd = fopen($file, 'rb')) {
            while (!feof($fd)) {
                print fread($fd, 1024);
            }
            fclose($fd);
        }
        exit;
    }
    private function copyFile($dir, $name)
    {
        //if (is_dir($name) || is_file($name)) {
            if (is_dir($dir)) {
                //перемещает, заменить на копирование
                if (rename($dir, $name)) {
                    $this->array['text'] = 'Директория успешно скопирована';
                } else $this->array['text'] = 'Ошибка при копировании директории';
                /////////////////////////////////
            } else if (is_file($dir)) {
                if (copy($dir, $name)) {
                    $this->array['text'] = 'Файл успешно скопирован';
                    //$name = $this->not_repeat($name);
                } else $this->array['text'] = 'Ошибка при копировании файла';
            } else $this->array['text'] = 'Ошибка. Файл/директория не найдены';
        //} else $this->array['text'] = 'Ошибка. Вы ввели несуществующий путь';
        $this->array['dir'] = $name;
    }
    private function moveFile($dir, $name){
        if (is_dir($dir)){
            if (rename($dir, $this->not_repeat($dir) . DIRECTORY_SEPARATOR . $name)){
                $this->array['text'] = 'Директория успешно переименована';
            } else $this->array['text'] = 'Ошибка при переименовании директории';
        } else if (is_file($dir)){
            if (rename($dir, $this->not_repeat($dir) . DIRECTORY_SEPARATOR . $name)){
                $this->array['text'] = 'Файл успешно переименован';
            } else $this->array['text'] = 'Ошибка при переименовании файла';
        } else $this->array['text'] = 'Ошибка. Файл/директория не найдены';

        $this->array['dir'] = $this->not_repeat($dir);
    }
    private function removeFile($dir){
        if (is_dir($dir)){
                if ($this->rrmdir($dir)) {
                    $this->array['text'] = 'Директория успешно удалена';
                    //echo $this->open($dir);
                } else $this->array['text'] = 'Ошибка при удалении директории';

                $this->array['dir'] = $this->not_repeat($dir);
            //}
        }
        if (is_file($dir)){
            if (unlink($dir)) {
                $this->array['text'] = 'Файл успешно удален';
                $this->array['dir'] = $this->not_repeat($dir);
                //echo $this->open($dir);
            } else $this->array['text'] = 'Ошибка при удалении файла';
        }
    }
    private function newfolder($dir, $name){
        $dir = htmlspecialchars($dir);
        if (!is_dir($dir . DIRECTORY_SEPARATOR . $name)){
            if (mkdir($dir . DIRECTORY_SEPARATOR . $name, 0777, true)) {
                $this->array['text'] = 'Директория создана';
                //echo $this->open($dir);
            } else $this->array['text'] = 'Ошибка при создании директории';
        } else $this->array['text'] = 'Директория существует';
        $this->array['dir'] = $dir;
    }
    private function zipARX($dir)
    {
        $file_folder = $this->not_repeat($dir); // папка с файлами

        $zip = new \ZipArchive(); // подгружаем библиотеку zip
        $zip_name = $file_folder . DIRECTORY_SEPARATOR . time() . ".zip"; // имя файла
        if ($zip->open( $zip_name, \ZIPARCHIVE::CREATE) !== TRUE) {
            $this->array['text'] = 'Ошибка при создании архива!';
        }

        $source = str_replace('\\', '/', realpath($dir));
        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    // Ignore "." and ".." folders
                    continue;
                }
                $file = realpath($file);
                if (is_dir($file) === true) {
                    $zip->addEmptyDir($files->getSubIterator()->getSubPath());
                }
                else
                    if (is_file($file) === true) {
                        $zip->addFromString($files->getSubIterator()->getSubPathname(), file_get_contents($file));
                    }
            }
        } else if (is_file($source) === true) {
                $zip->addFromString(basename($source), file_get_contents($source));
        }
        $zip->close();
        if (file_exists($zip_name)) {
            $this->array['text'] = 'Архив создан';
        } else $this->array['text'] = 'Ошибка при создании архива';
        $this->array['dir'] = $file_folder;
    }
    private function rrmdir($dir) {
        if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
        reset($objects);
        rmdir($dir);
            return 1;
        }
        return 0;
    }

}