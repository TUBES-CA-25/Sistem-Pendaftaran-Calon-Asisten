<?php

define('APP_NAME', getenv('APP_NAME') ?: 'IC-ASSIST');

/**
 * APP_URL mengikuti host yang sedang diakses.
 *
 * Dulu nilainya dipaku ke 'http://localhost/...'. Itu bekerja di komputer
 * server, tetapi begitu aplikasi dibuka dari perangkat lain (mis. HP lewat
 * http://192.168.1.8/...), seluruh tautan dan aset tetap menunjuk ke
 * 'localhost' - yang di HP berarti HP itu sendiri. Akibatnya CSS/JS gagal
 * dimuat dan navigasi melempar ke alamat yang salah.
 *
 * Host diambil dari permintaan sehingga alamat apa pun yang dipakai pengguna
 * (localhost, IP LAN, atau nama domain) menghasilkan tautan yang konsisten.
 * Variabel lingkungan APP_URL tetap didahulukan untuk deployment.
 */
$appUrl = getenv('APP_URL');

if (!$appUrl) {
    $skema = 'http';
    if ((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) {
        $skema = 'https';
    }

    // HTTP_HOST sudah termasuk port bila bukan 80/443.
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Hanya karakter yang sah untuk host, port, dan IPv6 literal yang
    // diloloskan. HTTP_HOST berasal dari header permintaan sehingga bisa
    // dipalsukan; tanpa penyaringan ini nilainya bisa disisipkan ke setiap
    // tautan yang dirender.
    if (!preg_match('/^[A-Za-z0-9\.\-\[\]:]+$/', $host)) {
        $host = 'localhost';
    }

    $appUrl = $skema . '://' . $host . '/Sistem-Pendaftaran-Calon-Asisten/public';
}

define('APP_URL', $appUrl);
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');