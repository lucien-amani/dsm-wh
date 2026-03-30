<?php
require_once 'config/database.php';
$pdo = getDBConnection();

try {
    // Add author_id column if it doesn't exist
    $pdo->exec("ALTER TABLE news ADD COLUMN author_id INT NULL AFTER id");
    
    // Add foreign key constraint (optional but good practice)
    $pdo->exec("ALTER TABLE news ADD CONSTRAINT fk_news_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL");
    
    // Set first admin as default author for existing news
    $pdo->exec("UPDATE news SET author_id = 1 WHERE author_id IS NULL");
    
    echo "Table news updated successfully.";
} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
