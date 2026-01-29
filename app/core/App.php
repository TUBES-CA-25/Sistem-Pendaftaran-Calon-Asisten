<?php

namespace App\Core;

use App\Core\Router;

class App
{
    public function run()
    {
        require_once __DIR__ . "/../config/Routes.php";

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get script directory (e.g., /project/public)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Normalize slashes for Windows consistency
        $scriptDir = str_replace('\\', '/', $scriptDir);
        
        // If script is in 'public' folder but URL doesn't contain 'public' (RewriteRule),
        // remove '/public' from scriptDir to match the request root
        if (substr($scriptDir, -7) === '/public' && strpos($path, '/public') === false) {
            $scriptDir = substr($scriptDir, 0, -7);
        }
        
        // Strip the script directory from the path
        if (strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }
        
        // Ensure path starts with /
        if (empty($path) || $path[0] !== '/') {
            $path = '/' . $path;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        Router::route($method, $path);
    }
}