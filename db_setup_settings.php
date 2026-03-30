<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', '');
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        key_name VARCHAR(100) UNIQUE NOT NULL,
        key_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insérer la valeur par défaut si elle n'existe pas
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (key_name, key_value) VALUES ('mail_from_name', 'supportdynamiquesamymagadju')");
    $stmt->execute();
    
    echo "Table settings prête.";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
