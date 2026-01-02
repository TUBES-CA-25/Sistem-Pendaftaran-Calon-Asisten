<?php

<<<<<<< HEAD
class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();
        
        // Controller
        if( isset($url[0]) ) {
            $u = ucfirst($url[0]);
            if( file_exists('../app/Controllers/' . $u . '/' . $u . 'Controller.php') ) {
                $this->controller = $u . 'Controller';
                unset($url[0]);
                require_once '../app/Controllers/' . $u . '/' . $this->controller . '.php';
            } elseif( file_exists('../app/Controllers/' . $u . '.php') ) {
                $this->controller = $u;
                unset($url[0]);
                require_once '../app/Controllers/' . $this->controller . '.php';
            }
        } else {
            // Default Controller
            if( file_exists('../app/Controllers/Home/HomeController.php') ) {
                $this->controller = 'HomeController';
                require_once '../app/Controllers/Home/HomeController.php';
            } elseif( file_exists('../app/Controllers/Home.php') ) {
                require_once '../app/Controllers/Home.php';
            }
        }
        
        $this->controller = new $this->controller;

        // Method
        if( isset($url[1]) ) {
            if( method_exists($this->controller, $url[1]) ) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Params
        if( !empty($url) ) {
            $this->params = array_values($url);
        }

        // Run Controller & Method, and send params if any
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        if( isset($_GET['url']) ) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}
=======
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
>>>>>>> origin/raihn
