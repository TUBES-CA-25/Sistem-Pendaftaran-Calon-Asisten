<?php
/**
 * Database Migration Runner
 * 
 * This script runs all pending migrations from the database folder.
 * Usage: Run this once to apply all migrations.
 * 
 * To use via command line:
 * php migrate.php
 * 
 * To use via browser:
 * Visit: http://yourapp.com/migrate.php
 */

// Load environment configuration
require_once(__DIR__ . '/../app/core/Env.php');
require_once(__DIR__ . '/../app/core/Database.php');

// Prevent public access - uncomment if you want to restrict access
// if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
//     http_response_code(403);
//     die('Access denied');
// }

try {
    $pdo = \App\Core\Database::getInstance();
    
    echo "Starting database migrations...\n";
    echo "================================\n\n";

    // Migration 1: Add reset token columns
    echo "[1] Adding reset_token columns to user table...\n";
    
    try {
        // Check if column exists
        $checkStmt = $pdo->query("SHOW COLUMNS FROM user LIKE 'reset_token'");
        $columnExists = $checkStmt->rowCount() > 0;

        if (!$columnExists) {
            $pdo->exec("ALTER TABLE user ADD COLUMN reset_token VARCHAR(255) NULL AFTER password");
            echo "✓ Added reset_token column\n";
        } else {
            echo "✓ reset_token column already exists\n";
        }

        $checkStmt = $pdo->query("SHOW COLUMNS FROM user LIKE 'reset_token_expiry'");
        $columnExists = $checkStmt->rowCount() > 0;

        if (!$columnExists) {
            $pdo->exec("ALTER TABLE user ADD COLUMN reset_token_expiry DATETIME NULL AFTER reset_token");
            echo "✓ Added reset_token_expiry column\n";
        } else {
            echo "✓ reset_token_expiry column already exists\n";
        }

        echo "✓ Migration completed successfully!\n\n";

    } catch (\PDOException $e) {
        echo "✗ Migration failed: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    echo "================================\n";
    echo "All migrations completed!\n";
    echo "You can now use the forgot password feature.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
