<?php
require_once 'config/Database.php';
require_once 'app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Add id_ruang_wawancara column
    $sql = "ALTER TABLE user ADD COLUMN id_ruang_wawancara INT(11) DEFAULT NULL";
    try {
        $db->query($sql);
        echo "Added id_ruang_wawancara column.\n";
    } catch (PDOException $e) {
        echo "Column id_ruang_wawancara might already exist or error: " . $e->getMessage() . "\n";
    }
    
    echo "Migration interview column completed.";

} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
?>
