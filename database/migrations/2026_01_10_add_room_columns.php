<?php
require_once 'config/Database.php';
require_once 'app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Fix 'modified' column first
    $sqlFix = "ALTER TABLE user MODIFY COLUMN modified TIMESTAMP NULL DEFAULT NULL";
    try {
        $db->query($sqlFix);
        echo "Fixed 'modified' column.\n";
    } catch (PDOException $e) {
        echo "Error fixing 'modified': " . $e->getMessage() . "\n";
    }

    // Add id_ruang_presentasi column
    $sql1 = "ALTER TABLE user ADD COLUMN id_ruang_presentasi INT(11) DEFAULT NULL";
    try {
        $db->query($sql1);
        echo "Added id_ruang_presentasi column.\n";
    } catch (PDOException $e) {
        echo "Column id_ruang_presentasi might already exist or error: " . $e->getMessage() . "\n";
    }

    // Add id_ruang_tes_tulis column
    $sql2 = "ALTER TABLE user ADD COLUMN id_ruang_tes_tulis INT(11) DEFAULT NULL";
    try {
        $db->query($sql2);
        echo "Added id_ruang_tes_tulis column.\n";
    } catch (PDOException $e) {
        echo "Column id_ruang_tes_tulis might already exist or error: " . $e->getMessage() . "\n";
    }
    
    echo "Migration completed.";

} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
?>
