<?php

namespace App\Core;

class Router
{
    private static array $routes = [];

    public static function add(string $method, string $path, $controller): void
    {
        self::$routes[$method][$path] = $controller;
    }

    public static function get(string $path, $controller): void
    {
        self::add('GET', $path, $controller);
    }

    public static function post(string $path, $controller): void
    {
        self::add('POST', $path, $controller);
    }

    public static function route(string $method, string $path): void
    {
        // 1. Try exact match first
        if (isset(self::$routes[$method][$path])) {
            $controller = self::$routes[$method][$path];
            if (is_callable($controller)) {
                call_user_func($controller);
                return;
            }
        }

        // 2. Try regex match for dynamic routes (e.g. /{page})
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $routePath => $controller) {
                // Only process routes with parameters
                if (strpos($routePath, '{') === false) {
                    continue;
                }

                // Convert {param} to regex group ([^/]+)
                // We use # as delimiter
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
                // Escape any other regex chars in the path if needed? 
                // For now assuming simple paths. 
                // To be safer, we could quote everything then unquote the braces part, but simplistic replacement is standard for simple routers.
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    // Remove full match from matches array
                    array_shift($matches);
                    
                    if (is_callable($controller)) {
                        // Pass matches as arguments to the controller
                        call_user_func_array($controller, $matches);
                        return;
                    }
                }
            }
        }

        // 3. 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
