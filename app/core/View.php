<?php
namespace App\Core;

class View {
    public static function render ($view,$folder, $data = []) {
        $filename = "../app/View/". $folder . "/". $view . ".php";

        if(file_exists($filename)) {
            if(!empty($data)) {
                extract($data);
            }
            require $filename;
        } else {
            redirect('miscellaneous/404');
        }
    }
}