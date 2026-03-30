<?php
require_once 'config/database.php';
$pdo = getDBConnection();
$counts = $pdo->query("SELECT status, COUNT(*) as count FROM comments GROUP BY status")->fetchAll();
echo "--- RÉSUMÉ DES COMMENTAIRES ---\n";
if (empty($counts)) {
    echo "La table 'comments' est vide.\n";
} else {
    foreach ($counts as $row) {
        echo "- " . ucfirst($row['status']) . " : " . $row['count'] . "\n";
    }
}
$recent = $pdo->query("SELECT user_name, content, status, created_at FROM comments ORDER BY created_at DESC LIMIT 5")->fetchAll();
if (!empty($recent)) {
    echo "\n--- 5 DERNIERS COMMENTAIRES ---\n";
    foreach ($recent as $c) {
        echo "[" . $c['status'] . "] " . $c['user_name'] . " : " . substr($c['content'], 0, 50) . "...\n";
    }
}
