<?php

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


       public function view($view, $data = []) {
        require_once '../app/View/' . $view . '.php';
    }

    // Fungsi untuk memanggil Model (INI YANG HILANG SEBELUMNYA)
    public function model($model) {
        // Kita asumsikan semua Model ada di namespace App\Model
        $className = 'App\\Model\\' . $model;
        
        // Cek apakah class ada (opsional, tapi bagus untuk debugging)
        if (class_exists($className)) {
            return new $className;
        } else {
            // Debugging: Jika error class not found muncul
            die("Error: Model $className tidak ditemukan. Cek nama file dan namespace.");
        }
    }

}