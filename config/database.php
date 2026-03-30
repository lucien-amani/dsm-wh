<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Lucien-Amani8084LOCAL');
define('DB_NAME', 'dsm_website');

// Configuration générale du site
define('SITE_NAME', 'Dynamique Samy Magadju/Wema ni Hakiba ASBL');
define('SITE_TAGLINE', 'Ensemble pour le développement intégral');

// Détection dynamique et robuste de l'URL racine du site
if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
                || ($_SERVER['SERVER_PORT'] ?? 80) == 443 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false)) ? "https://" : "http://";
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Détection intelligente du dossier racine
    $webPath = '';
    // Si on est en local (sur localhost), on garde le dossier /dsm
    if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
        $webPath = '/dsm';
    } 
    // Sinon (si c'est un tunnel ou un domaine), on vérifie si le projet est dans un sous-dossier
    else {
        $projectRoot = realpath(__DIR__ . '/..');
        $docRoot     = realpath($_SERVER['DOCUMENT_ROOT']);
        if ($projectRoot && $docRoot && strpos($projectRoot, $docRoot) === 0) {
            $webPath = str_replace($docRoot, '', $projectRoot);
            $webPath = str_replace('\\', '/', $webPath);
            $webPath = rtrim($webPath, '/');
        }
    }
    
    define('SITE_URL', rtrim($protocol . $host . $webPath, '/'));
}
define('ADMIN_EMAIL', 'admin@dsm-samy.com');

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // AUTO-MIGRATION: Ajouter la colonne author_id si elle n'existe pas
        // Cela permet de lier chaque article à un utilisateur réel de la table 'users'
        try {
            $check = $pdo->query("SHOW COLUMNS FROM news LIKE 'author_id'");
            if (!$check->fetch()) {
                $pdo->exec("ALTER TABLE news ADD COLUMN author_id INT NULL AFTER id");
                // Attribuer l'article au premier administrateur par défaut
                $pdo->exec("UPDATE news SET author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE author_id IS NULL");
            }
        } catch (Exception $migrationError) {
            // On ignore silencieusement les erreurs de migration pour ne pas bloquer le site
        }
        
        // AUTO-MIGRATION: Tables pour les logs
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS admin_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS log_exports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } catch (Exception $migrationError) {}

        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        die("Erreur de connexion à la base de données");
    }
}

// Fonction pour générer un slug
function generateSlug($text) {
    $text = trim($text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
    $text = strtolower($text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// Fonction pour formater une date en français
function formatDateFR($date) {
    $months = [
        'January' => 'janvier', 'February' => 'février', 'March' => 'mars',
        'April' => 'avril', 'May' => 'mai', 'June' => 'juin',
        'July' => 'juillet', 'August' => 'août', 'September' => 'septembre',
        'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'
    ];
    
    $timestamp = strtotime($date);
    $formatted = date('d F Y', $timestamp);
    
    foreach ($months as $en => $fr) {
        $formatted = str_replace($en, $fr, $formatted);
    }
    
    return $formatted;
}

// Fonction pour tronquer du texte
function truncate($text, $length = 150, $suffix = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= $suffix;
    }
    return $text;
}
// Fonction pour compresser et redimensionner une image
function compressImage($source, $destination, $quality = 80, $maxWidth = 1200) {
    if (!extension_loaded('gd')) return false;
    
    $info = getimagesize($source);
    if ($info === false) return false;

    $width = $info[0];
    $height = $info[1];
    
    // Déterminer le type d'image
    switch ($info['mime']) {
        case 'image/jpeg': $img = imagecreatefromjpeg($source); break;
        case 'image/png':  $img = imagecreatefrompng($source); break;
        case 'image/webp': $img = imagecreatefromwebp($source); break;
        case 'image/gif':  $img = imagecreatefromgif($source); break;
        default: return false;
    }

    // Calculer les nouvelles dimensions
    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = floor($height * ($maxWidth / $width));
        $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
        
        // Gérer la transparence pour PNG/WebP
        imagealphablending($tmpImg, false);
        imagesavealpha($tmpImg, true);
        
        imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $img = $tmpImg;
    }

    // Sauvegarder en JPEG (meilleure compression pour le web)
    return imagejpeg($img, $destination, $quality);
}
// Fonction pour enregistrer une action admin dans les logs
function logAdminAction($userId, $action, $ip = null) {
    if (!$ip) {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }
    }
    
    try {
        $pdo = getDBConnection();
        
        // Anti-duplication : ne pas logger la même action exacte pour le même utilisateur et IP dans les 30 dernières secondes
        $stmt_check = $pdo->prepare("SELECT id FROM admin_logs 
                                    WHERE user_id = :u AND action = :a AND ip_address = :ip 
                                    AND created_at > DATE_SUB(NOW(), INTERVAL 30 SECOND) 
                                    LIMIT 1");
        $stmt_check->execute([':u' => $userId, ':a' => $action, ':ip' => $ip]);
        if ($stmt_check->fetch()) {
            return true; // Déjà loggé très récemment
        }

        $stmt = $pdo->prepare("INSERT INTO admin_logs (user_id, action, ip_address) VALUES (:user_id, :action, :ip)");
        return $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':ip' => $ip
        ]);
    } catch (Exception $e) {
        error_log("Erreur de log admin : " . $e->getMessage());
        return false;
    }
}
