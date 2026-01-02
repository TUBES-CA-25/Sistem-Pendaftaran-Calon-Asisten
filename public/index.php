<?php
<<<<<<< HEAD

if( !session_id() ) {
    session_start();
}

require_once '../config/config.php';
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

$app = new App;
=======
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../routes/autoload.php';

>>>>>>> origin/raihn
