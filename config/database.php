<?php
// EN-TÊTES DE SÉCURITÉ GLOBAUX
if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://platform.twitter.com https://cdn.jsdelivr.net https://cdn.tiny.cloud; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdn.tiny.cloud; font-src 'self' https://fonts.gstatic.com https://cdn.tiny.cloud; img-src 'self' data: blob: https:; connect-src 'self' https://cdn.tiny.cloud; iframe-src 'self' https://www.youtube.com https://platform.twitter.com;");
}
// Configuration de la base de données
require_once __DIR__ . '/env.php';
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'dsm_website');

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
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $docRoot     = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    
    $webPath = '';
    if ($projectRoot && $docRoot && strpos($projectRoot, $docRoot) === 0) {
        $webPath = substr($projectRoot, strlen($docRoot));
        $webPath = '/' . trim($webPath, '/');
    }
    
    define('SITE_URL', $_ENV['SITE_URL'] ?? rtrim($protocol . $host . $webPath, '/'));
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

        // AUTO-MIGRATION: Colonne nanoid pour news et projects
        // Les NanoIDs remplacent les hashids dans les URLs (ex: /view/news/V1StGq8YU5/slug)
        foreach (['news', 'projects'] as $_tbl) {
            try {
                $chk = $pdo->query("SHOW COLUMNS FROM `{$_tbl}` LIKE 'nanoid'");
                if (!$chk->fetch()) {
                    $pdo->exec("ALTER TABLE `{$_tbl}` ADD COLUMN nanoid VARCHAR(12) NULL AFTER id");
                    $pdo->exec("ALTER TABLE `{$_tbl}` ADD UNIQUE INDEX idx_{$_tbl}_nanoid (nanoid)");
                }
                // Peupler les enregistrements sans nanoid (par lots pour éviter le timeout)
                $rows = $pdo->query("SELECT id FROM `{$_tbl}` WHERE nanoid IS NULL OR nanoid = '' LIMIT 50")->fetchAll(PDO::FETCH_COLUMN);
                $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $upd   = $pdo->prepare("UPDATE `{$_tbl}` SET nanoid = ? WHERE id = ?");
                foreach ($rows as $_rid) {
                    do {
                        $_nano = '';
                        for ($__i = 0; $__i < 10; $__i++) {
                            $_nano .= $chars[random_int(0, 61)];
                        }
                        $exists = $pdo->prepare("SELECT 1 FROM `{$_tbl}` WHERE nanoid = ?");
                        $exists->execute([$_nano]);
                    } while ($exists->fetch());
                    $upd->execute([$_nano, $_rid]);
                }
            } catch (Exception $_e) { /* silencieux */ }
        }

        // AUTO-MIGRATION: Table pour le rate limiting
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                attempts INT DEFAULT 1,
                last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_ip (ip_address)
            )");
        } catch (Exception $migrationError) {}

        // AUTO-MIGRATION: Soft Delete pour les actualités
        try {
            $pdo->exec("ALTER TABLE news ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
            $pdo->exec("ALTER TABLE projects ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
        } catch (Exception $migrationError) {}

        // AUTO-MIGRATION: Table settings
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                key_name VARCHAR(50) PRIMARY KEY,
                key_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
            // Initialiser membership_enabled si inexistant
            $pdo->exec("INSERT IGNORE INTO settings (key_name, key_value) VALUES ('membership_enabled', '1')");
        } catch (Exception $migrationError) {}

        // AUTO-MIGRATION: Colonne member_uuid et statut Brouillon pour la table adhesions
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM adhesions LIKE 'member_uuid'");
            if (!$chk->fetch()) {
                $pdo->exec("ALTER TABLE adhesions ADD COLUMN member_uuid VARCHAR(32) NULL AFTER file_number");
            }
        } catch (Exception $migrationError) {}
        try {
            // Ajouter 'Brouillon' à l'enum status si absent
            $col = $pdo->query("SHOW COLUMNS FROM adhesions LIKE 'status'")->fetch();
            if ($col && strpos($col['Type'], 'Brouillon') === false) {
                $pdo->exec("ALTER TABLE adhesions MODIFY COLUMN status ENUM('En attente','Approuvée','Rejetée','Brouillon') DEFAULT 'En attente'");
            }
        } catch (Exception $migrationError) {}

        // AUTO-MIGRATION: Table pour les publicités (Ads)
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS ads (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                image VARCHAR(255) NOT NULL,
                link VARCHAR(255) NOT NULL,
                slot VARCHAR(50) NOT NULL,
                start_date DATETIME NOT NULL,
                end_date DATETIME NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                views INT DEFAULT 0,
                clicks INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (Exception $migrationError) {}

        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        
        $db_error_file = __DIR__ . '/../pages/db-error.php';
        if (file_exists($db_error_file)) {
            header('HTTP/1.1 500 Internal Server Error');
            include $db_error_file;
            exit;
        }
        
        die("Erreur de connexion à la base de données");
    }
}

// Fonction pour générer un slug
if (!function_exists('generateSlug')) {
    function generateSlug($text) {
        $text = trim($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        $text = strtolower($text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}

// Fonction pour formater une date en français
if (!function_exists('formatDateFR')) {
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
}

// Fonction pour tronquer du texte
if (!function_exists('truncate')) {
    function truncate($text, $length = 150, $suffix = '...') {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length);
            $text = substr($text, 0, strrpos($text, ' '));
            $text .= $suffix;
        }
        return $text;
    }
}
// Fonction pour compresser et redimensionner une image
if (!function_exists('compressImage')) {
    function compressImage($source, $destination, $quality = 80, $maxWidth = 1200) {
        if (!extension_loaded('gd')) {
            // Fallback: simple copie si GD n'est pas dispo
            return move_uploaded_file($source, $destination) || copy($source, $destination);
        }
        
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
            default: return move_uploaded_file($source, $destination) || copy($source, $destination);
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
        $result = imagejpeg($img, $destination, $quality);
        imagedestroy($img);
        return $result;
    }
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
