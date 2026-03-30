<?php
$pdo = new PDO('mysql:host=localhost;dbname=dsm_website', 'root', 'Lucien-Amani8084LOCAL');
echo "TABLE: news" . PHP_EOL;
$stmt = $pdo->query('SHOW COLUMNS FROM news');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
}
echo PHP_EOL . "TABLE: projects" . PHP_EOL;
$stmt = $pdo->query('SHOW COLUMNS FROM projects');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
}
