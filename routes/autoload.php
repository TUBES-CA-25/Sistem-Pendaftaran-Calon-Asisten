<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Load Environment Variables
require_once __DIR__ . '/../app/Core/Env.php';
App\Core\Env::load(__DIR__ . '/../.env');

$config = glob(__DIR__ . '/../config/*.php');
foreach ($config as $file) {
    require_once $file;
}

$core = glob(__DIR__ . '/../app/core/*.php');
foreach ($core as $file) {
    require_once $file;
}

spl_autoload_register(function($class) {
    $class = str_replace("App\\", "app/", $class); 
    $file = __DIR__ . '/../' . str_replace("\\", "/", $class) . ".php";

    if (file_exists($file)) {
        require $file;
    }

});

APP_DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);