<?php

namespace App\Core;
use BadMethodCallException;
abstract class Controller {
    public function view($view, $data = []) {
        $viewFile = "../app/View/" . $view . ".php";
        if (file_exists($viewFile)) {
            if (!empty($data)) {
                extract($data);
            }
            require $viewFile;
        } else {
            // Optional: fallback or error
            // echo "View not found: " . $viewFile;
            throw new \Exception("View not found: " . $viewFile);
        }
    }

    public function __call($name, $arguments) {
        throw new BadMethodCallException (sprintf(
            'Method "%s" is not Implemented in class "%s" .',
            $name,
            get_class($this)
        ));
    }
}