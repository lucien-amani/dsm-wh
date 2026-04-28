<?php
/**
 * scratch/check_nanoid.php
 * Vérifie que le système NanoID est opérationnel.
 */
require_once 'config/database.php';
require_once 'config/helpers.php';

$pdo = getDBConnection();

echo "--- TEST NANOID ---\n\n";

// 1. Tester la génération
$nano = generateNanoId();
echo "Génération NanoID : $nano (Longueur: " . strlen($nano) . ")\n";

// 2. Vérifier la BDD
foreach (['news', 'projects'] as $tbl) {
    $count = $pdo->query("SELECT COUNT(*) FROM $tbl WHERE nanoid IS NOT NULL AND nanoid != ''")->fetchColumn();
    $total = $pdo->query("SELECT COUNT(*) FROM $tbl")->fetchColumn();
    echo "Table $tbl : $count / $total records ont un NanoID.\n";
    
    if ($total > 0) {
        $sample = $pdo->query("SELECT id, nanoid, slug FROM $tbl LIMIT 1")->fetch();
        $url = getUrl($tbl, $sample['id'], $sample['slug'], $sample['nanoid']);
        echo "Exemple URL $tbl : $url\n";
    }
}

echo "\n--- FIN DU TEST ---\n";
