<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
    $is_final = isset($_POST['finalize']) && $_POST['finalize'] == '1';

    if (!$id) {
        // Create new entry
        $stmt = $pdo->prepare("INSERT INTO adhesions (last_name, post_name, first_name, status) VALUES ('', '', '', 'Brouillon')");
        $stmt->execute();
        $id = $pdo->lastInsertId();
    }

    if ($step == 1) {
        $stmt = $pdo->prepare("UPDATE adhesions SET last_name = ?, post_name = ?, first_name = ?, birth_place = ?, birth_date = ?, nationality = ?, civil_status = ?, children_count = ?, profession = ? WHERE id = ?");
        $stmt->execute([
            $_POST['last_name'] ?? '',
            $_POST['post_name'] ?? '',
            $_POST['first_name'] ?? '',
            $_POST['birth_place'] ?? '',
            $_POST['birth_date'] ?: null,
            $_POST['nationality'] ?? 'Congolaise',
            $_POST['civil_status'] ?? '',
            (int)($_POST['children_count'] ?? 0),
            $_POST['profession'] ?? '',
            $id
        ]);
    } elseif ($step == 2) {
        $stmt = $pdo->prepare("UPDATE adhesions SET province = ?, city_district = ?, commune_territory = ?, quarter_secteur = ?, address_details = ?, phone1 = ?, phone2 = ?, email = ? WHERE id = ?");
        $stmt->execute([
            $_POST['province'] ?? '',
            $_POST['city_district'] ?? '',
            $_POST['commune_territory'] ?? '',
            $_POST['quarter_secteur'] ?? '',
            $_POST['address_details'] ?? '',
            $_POST['phone1'] ?? '',
            $_POST['phone2'] ?? '',
            $_POST['email'] ?? '',
            $id
        ]);
    } elseif ($step == 3) {
        // Handle Photo Upload
        $photo_name = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['photo']['tmp_name'];
            $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = 'photo_' . time() . '_' . uniqid() . '.' . $extension;
            move_uploaded_file($tmp_name, __DIR__ . '/../uploads/adhesions/' . $photo_name);
            
            $stmt = $pdo->prepare("UPDATE adhesions SET photo = ? WHERE id = ?");
            $stmt->execute([$photo_name, $id]);
        }

        $stmt = $pdo->prepare("UPDATE adhesions SET member_category = ?, membership_fee = ? WHERE id = ?");
        $stmt->execute([
            $_POST['member_category'] ?? '',
            $_POST['membership_fee'] ?? '',
            $id
        ]);
    }

    if ($is_final) {
        $stmt = $pdo->prepare("UPDATE adhesions SET status = 'En attente' WHERE id = ?");
        $stmt->execute([$id]);
    }

    echo json_encode(['success' => true, 'id' => $id, 'message' => 'Étape enregistrée']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
