<?php
/**
 * admin/includes/auth.php
 * Gère la session admin et le timeout d'inactivité.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timeout par défaut : 10 minutes d'inactivité (600 secondes)
define('ADMIN_SESSION_TIMEOUT', 600);

if (!isset($_SESSION['admin_id'])) {
    // Si la requête est une requête AJAX, on renvoie un code 401
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(401);
        exit(json_encode(['error' => 'Session expirée']));
    }
    
    header('Location: ' . SITE_URL . '/admin/connexion');
    exit;
}

// Vérification du timeout
if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    
    if ($elapsed > ADMIN_SESSION_TIMEOUT) {
        // Session expirée
        session_unset();
        session_destroy();
        
        // Redirection avec un message
        header('Location: ' . SITE_URL . '/admin/connexion?timeout=1');
        exit;
    }
}

// Mise à jour du temps de dernière activité
$_SESSION['last_activity'] = time();

// PROTECTION CSRF GLOBALE : Vérifier toutes les requêtes POST dans l'administration
require_once dirname(__DIR__, 2) . '/config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        die("Erreur de sécurité : Jeton CSRF invalide ou manquant.");
    }
}
