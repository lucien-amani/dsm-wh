<?php
$pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', 'Lucien-Amani8084LOCAL');
try {
    // Ajouter author_id à la table projects
    $pdo->exec("ALTER TABLE projects ADD COLUMN author_id INT AFTER id");
    echo "Colonne 'author_id' ajoutée à la table projects." . PHP_EOL;
} catch (Exception $e) {
    echo "Erreur (peut-être déjà existante) : " . $e->getMessage() . PHP_EOL;
}
