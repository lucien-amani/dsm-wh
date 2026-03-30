<?php
require_once 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query("DESCRIBE news");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols, JSON_PRETTY_PRINT);
