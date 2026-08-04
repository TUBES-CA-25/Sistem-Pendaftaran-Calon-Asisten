<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Environment Variables (Env.php is in the same directory)
require_once __DIR__ . '/Env.php';
App\Core\Env::load(__DIR__ . '/../../.env');

// Config (Sibling folder ../config)
//
// Config.php DIIKUTKAN di sini (dulu dikecualikan). Alasannya: kelas
// App\Config\Config dipakai public/index.php:28 dan dulu hanya bisa termuat
// lewat autoloader namespace, yang memetakan App\Config\Config ->
// app/Config/Config.php dengan C BESAR — padahal folder aslinya `app/config`
// huruf kecil. Di Windows lolos karena filesystem case-insensitive, tetapi di
// server Linux kelas itu tidak akan pernah ditemukan dan aplikasi fatal saat
// baris pertama index.php.
//
// Routes.php tetap dikecualikan: berkas itu memuat pemanggilan Router yang
// harus dijalankan belakangan, bukan saat autoload.
$config = glob(__DIR__ . '/../config/*.php');
foreach ($config as $file) {
    if (basename($file) !== 'Routes.php') {
        require $file;
    }
}

// Core (Current directory)
$core = glob(__DIR__ . '/*.php');
foreach ($core as $file) {
    // Avoid re-requiring self or Config files if any
    if (basename($file) !== 'autoload.php') {
        require_once $file;
    }
}

spl_autoload_register(function($class) {
    $class = str_replace("App\\", "app/", $class); 
    $file = __DIR__ . '/../../' . str_replace("\\", "/", $class) . ".php";

    if (file_exists($file)) {
        require $file;
    }

});

APP_DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);