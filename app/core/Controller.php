<?php

<<<<<<< HEAD
class Controller {
    public function view($view, $data = [])
    {
        require_once '../app/View/' . $view . '.php';
    }

    public function model($model)
    {
        require_once '../app/Model/' . $model . '.php';
        return new $model;
    }
}
=======
namespace App\Core;
use BadMethodCallException;
abstract class Controller {
    public function __call($name, $arguments) {
        throw new BadMethodCallException (sprintf(
            'Method "%s" is not Implemented in class "%s" .',
            $name,
            get_class($this)
        ));
    }
}
>>>>>>> origin/raihn
