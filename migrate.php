<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/database.php';
$pdo = getDBConnection();

try {
    // Add author_id column
    $pdo->exec("ALTER TABLE news ADD COLUMN author_id INT NULL AFTER id");
    echo "Column author_id added.\n";
} catch (PDOException $e) {
    echo "Info: Column author_id might already exist or: " . $e->getMessage() . "\n";
}

try {
    // Set default author for existing
    $pdo->exec("UPDATE news SET author_id = 1 WHERE author_id IS NULL");
    echo "Default author set.\n";
} catch (PDOException $e) {
    echo "Error updating existing: " . $e->getMessage() . "\n";
}
