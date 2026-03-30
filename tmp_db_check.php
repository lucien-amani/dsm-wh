<?php
$pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', 'Lucien-Amani8084LOCAL');
$stmt = $pdo->query('SHOW COLUMNS FROM users');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
}
