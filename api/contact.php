<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/mail.php';
require_once '../config/router.php';
require_once '../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et valider les données
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Le nom est requis';
}

if (empty($email)) {
    $errors[] = 'L\'email est requis';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}

if (empty($subject)) {
    $errors[] = 'Le sujet est requis';
}

if (empty($message)) {
    $errors[] = 'Le message est requis';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message doit contenir au moins 10 caractères';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, phone, subject, message) 
        VALUES (:name, :email, :phone, :subject, :message)
    ");
    
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':subject' => $subject,
        ':message' => $message
    ]);

    // Envoyer l'email de notification à l'admin
    $mailSubject = "Nouveau message de contact : " . $subject;
    $mailBody = "
        <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
            <h2 style='color: #059669;'>Nouveau message reçu</h2>
            <p><strong>De :</strong> {$name} ({$email})</p>
            <p><strong>Téléphone :</strong> " . ($phone ?: 'Non renseigné') . "</p>
            <p><strong>Sujet :</strong> {$subject}</p>
            <hr style='border: 0; border-top: 1px solid #eee;'>
            <p><strong>Message :</strong></p>
            <p style='background: #f9fafb; padding: 15px; border-radius: 8px;'>{$message}</p>
        </div>
    ";
    
    sendMail(MAIL_USER, $mailSubject, $mailBody);
    
    echo json_encode([
        'success' => true,
        'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.'
    ]);
    
} catch (Exception $e) {
    error_log("Erreur lors de l'enregistrement du message: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.'
    ]);
}
