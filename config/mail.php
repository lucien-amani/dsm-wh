<?php
/**
 * Configuration SMTP
 */

// Paramètres statiques
// Paramètres SMTP (Chargés depuis .env)
define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? 587); 
define('MAIL_USER', $_ENV['MAIL_USER'] ?? 'dsamymagadju@gmail.com');
define('MAIL_PASS', $_ENV['MAIL_PASS'] ?? 'dyva zbhq skzb plxd');
define('MAIL_FROM_EMAIL', $_ENV['MAIL_FROM_EMAIL'] ?? 'dsamymagadju@gmail.com');
define('MAIL_AUTH', true);
define('MAIL_ENCRYPTION', 'tls');

/**
 * Paramètres dynamiques (chargés depuis la BD si disponible)
 */
$default_from_name = 'supportdynamiquesamymagadju';

try {
    // On essaie de récupérer le nom depuis la table settings
    // On utilise fetchColumn pour plus de simplicité
    $pdo_mail = getDBConnection();
    $stmt_mail = $pdo_mail->prepare("SELECT key_value FROM settings WHERE key_name = 'mail_from_name' LIMIT 1");
    $stmt_mail->execute();
    $db_name = $stmt_mail->fetchColumn();
    
    define('MAIL_FROM_NAME', $db_name ?: $default_from_name);
} catch (Exception $e) {
    define('MAIL_FROM_NAME', $default_from_name);
}
