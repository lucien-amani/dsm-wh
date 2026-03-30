<?php
require_once __DIR__ . '/../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$type = $_POST['type'] ?? null; // 'news' or 'comment'

if (!$id || !$type) {
    echo json_encode(['success' => false]);
    exit;
}

$pdo = getDBConnection();

// Simple anti-spam: store liked IDs in session
$session_key = "liked_" . $type . "_" . $id;
if (isset($_SESSION[$session_key])) {
    echo json_encode(['success' => false, 'msg' => 'Déjà liké']);
    exit;
}

if ($type === 'news') {
    $stmt = $pdo->prepare("UPDATE news SET likes = likes + 1 WHERE id = ?");
    $stmt->execute([$id]);
} elseif ($type === 'comment') {
    $stmt = $pdo->prepare("UPDATE comments SET likes = likes + 1 WHERE id = ?");
    $stmt->execute([$id]);
}

$_SESSION[$session_key] = true;

// Get new count
if ($type === 'news') {
    $stmt = $pdo->prepare("SELECT likes FROM news WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT likes FROM comments WHERE id = ?");
}
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

echo json_encode(['success' => true, 'count' => $count]);
