<?php
require 'c:/xampp/htdocs/dsm/config/database.php';
$pdo = getDBConnection();
try {
    $pdo->exec('ALTER TABLE news ADD COLUMN embed_code TEXT NULL AFTER content');
    echo 'news altered. ';
} catch(Exception $e) {
    echo 'news err: ' . $e->getMessage() . ' ';
}
try {
    $pdo->exec('ALTER TABLE projects ADD COLUMN embed_code TEXT NULL AFTER content');
    echo 'projects altered.';
} catch(Exception $e) {
    echo 'proj err: ' . $e->getMessage() . ' ';
}
