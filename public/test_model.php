<?php
require_once '../config/Database.php';
require_once '../app/Model/User.php';

$user = new User();
$data = $user->getAllUser();

echo "<pre>";
print_r($data);
echo "</pre>";
