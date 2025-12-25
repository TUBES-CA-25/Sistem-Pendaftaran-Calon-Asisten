<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_tubes";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("❌ Koneksi database GAGAL: " . mysqli_connect_error());
}

echo "✅ Koneksi database BERHASIL";
