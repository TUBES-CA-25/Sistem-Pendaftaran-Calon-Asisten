<?php
require_once __DIR__ . '/app/Core/autoload.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY (email)
    )";
    
    $db->exec($sql);
    echo "Table 'password_resets' created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
