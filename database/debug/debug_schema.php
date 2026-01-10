<?php
require_once 'config/Database.php';
require_once 'app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("DESCRIBE user");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in 'user' table:\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
