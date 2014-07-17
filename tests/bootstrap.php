<?php
if (!@include __DIR__ . '/../vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
}

//function loader($class)
//{
    //$file = $class . '.php';
    //if (file_exists($file)) {
        //require $file;
    //}
//}

//spl_autoload_register('loader');
