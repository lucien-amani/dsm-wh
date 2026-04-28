<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/router.php';
require_once '../config/helpers.php';
require_once '../config/database.php';
require_once '../config/mail.php';
require_once '../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse email est requise']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse email n\'est pas valide']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Vérifier si déjà abonné
    $stmt = $pdo->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $subscriber = $stmt->fetch();

    if ($subscriber) {
        if ($subscriber['status'] === 'active') {
            echo json_encode(['success' => false, 'message' => 'Vous êtes déjà abonné à notre newsletter.', 'type' => 'info']);
            exit;
        } else {
            // Réactiver l'abonnement
            $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE id = :id");
            $stmt->execute([':id' => $subscriber['id']]);
        }
    } else {
        // Nouvel abonnement
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (:email)");
        $stmt->execute([':email' => $email]);
    }

    // Envoyer un email de confirmation de bienvenue (Optionnel mais premium)
    $unsubscribeLink = SITE_URL . "/api/unsubscribe.php?email=" . base64_encode($email);
    $mailSubject = "Bienvenue dans la newsletter de la Dynamique Samy Magadju/Wema ni Hakiba ASBL (DSM)";
    $mailBody = "
        <div style='font-family: \"Outfit\", sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
            <div style='background: #0f172a; padding: 40px; text-align: center; border-bottom: 4px solid #f59e0b;'>
                <h1 style='color: white; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -1px;'>BIENVENUE !</h1>
                <p style='color: #94a3b8; font-size: 11px; font-weight: 700; margin-top: 5px; text-transform: uppercase; letter-spacing: 2px;'>La Dynamique Ir. Samy Magadju</p>
            </div>
            <div style='padding: 40px;'>
                <p style='font-size: 16px; font-weight: 500;'>Bonjour,</p>
                <p style='font-size: 15px; color: #475569;'>Merci de vous être abonné à la newsletter de <strong>Ir. Samy Magadju</strong>. Nous sommes ravis de vous compter parmi nos fidèles lecteurs !</p>
                <p style='font-size: 15px; color: #475569;'>Vous recevrez désormais en priorité nos actualités, les rapports de nos projets sur le terrain et nos analyses exclusives sur le développement de notre province.</p>
                
                <div style='margin-top: 40px; padding-top: 30px; border-top: 1px solid #f1f5f9; text-align: center;'>
                    <p style='font-size: 11px; color: #94a3b8;'>
                        Vous recevez cet e-mail car vous vous êtes inscrit sur notre site.<br>
                        <a href='{$unsubscribeLink}' style='color: #f59e0b; text-decoration: underline; font-weight: 700;'>Se désabonner de cette liste</a>
                    </p>
                </div>
            </div>
        </div>
    ";

    
    sendMail($email, $mailSubject, $mailBody);

    echo json_encode([
        'success' => true,
        'message' => 'Félicitations ! Vous êtes maintenant abonné à notre newsletter.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}
