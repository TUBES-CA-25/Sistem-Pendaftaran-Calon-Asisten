<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$config = glob(__DIR__ . '/../config/*.php');
foreach ($config as $file) {
    require $file;
}

$core = glob(__DIR__ . '/../app/core/*.php');
foreach ($core as $file) {
    require $file;
}

spl_autoload_register(function($class) {
    $class = str_replace("App\\", "app/", $class); 
    $file = __DIR__ . '/../' . str_replace("\\", "/", $class) . ".php";

    if (file_exists($file)) {
        require $file;
    }

});

APP_DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);