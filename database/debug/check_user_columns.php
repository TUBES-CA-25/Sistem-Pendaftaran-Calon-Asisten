<?php
require_once 'config/Database.php';
require_once 'app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $stmt = $db->query("DESCRIBE user");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
