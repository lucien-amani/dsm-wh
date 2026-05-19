<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$type = isset($_POST['type']) ? $_POST['type'] : ''; // 'view' ou 'click'

if (!$id || !in_array($type, ['view', 'click'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

$pdo = getDBConnection();

if ($type === 'view') {
    $stmt = $pdo->prepare("UPDATE ads SET views = views + 1 WHERE id = ?");
} else {
    $stmt = $pdo->prepare("UPDATE ads SET clicks = clicks + 1 WHERE id = ?");
}

if ($stmt->execute([$id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
