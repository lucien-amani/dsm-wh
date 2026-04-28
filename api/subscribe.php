<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['endpoint'])) {
        $pdo = getDBConnection();
        
        // Vérifier si l'endpoint existe déjà
        $stmt = $pdo->prepare("SELECT id FROM push_subscriptions WHERE endpoint = :endpoint");
        $stmt->execute([':endpoint' => $data['endpoint']]);
        
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO push_subscriptions (endpoint, p256dh, auth) VALUES (:endpoint, :p256dh, :auth)");
            $stmt->execute([
                ':endpoint' => $data['endpoint'],
                ':p256dh'   => $data['keys']['p256dh'] ?? '',
                ':auth'     => $data['keys']['auth'] ?? ''
            ]);
        }
        
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid data']);
