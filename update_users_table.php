<?php
$pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', 'Lucien-Amani8084LOCAL');
try {
    // 1. Modifier le ENUM de la colonne 'role'
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Superadmin', 'Editor') DEFAULT 'Editor'");
    echo "Colonne 'role' modifiée avec succès." . PHP_EOL;

    // 2. Ajouter la colonne 'phone'
    $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
    echo "Colonne 'phone' ajoutée avec succès." . PHP_EOL;

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . PHP_EOL;
}
