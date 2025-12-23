<?php

namespace App\Core;

use App\Core\Router;

class App
{
    public function run()
    {
        require_once "../routes/web.php";

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $path = str_replace('/Sistem-Pendaftaran-Calon-Asisten/public', '', $path);
        
        if ($path == '') { 
            $path = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        Router::route($method, $path);
    }
}