<?php
$pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', '');
$stmt = $pdo->query('SHOW COLUMNS FROM news');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
