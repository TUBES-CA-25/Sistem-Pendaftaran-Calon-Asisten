<?php
namespace App\Core;

class View {
    public static function render ($view,$folder, $data = []) {
        $filename = dirname(__DIR__) . "/View/". $folder . "/". $view . ".php";

        if(file_exists($filename)) {
            if(!empty($data)) {
                extract($data);
            }
            require $filename;
        } else {
            // Debugging: Log the missing file path
            error_log("View file not found: " . $filename);
            redirect('miscellaneous/404');
        }
    }
}