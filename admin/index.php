<?php
/**
 * Admin Front Controller
 * Gère les URLs propres pour l'interface d'administration
 * 
 * Routes :
 *   /admin               → login.php (ou dashboard si connecté)
 *   /admin/tableau-de-bord → dashboard.php
 *   /admin/actualites      → news.php
 *   /admin/actualites/modifier → news_edit.php
 *   /admin/projets         → projects.php
 *   /admin/projets/modifier → projects_edit.php
 *   /admin/commentaires    → comments.php
 *   /admin/profil          → profile.php
 *   /admin/deconnexion     → logout.php
 */

// Charger la config (qui initialise $hashids via router.php)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/router.php';

// Définir la constante pour que les fichiers admin sachent qu'ils sont inclus via le routeur
define('ADMIN_ROUTER', true);

// Déterminer la route demandée
// REQUEST_URI ressemble à : /base/admin/tableau-de-bord ou /admin
$request_uri = $_SERVER['REQUEST_URI'];

// Supprimer la query string pour le routage
$path = parse_url($request_uri, PHP_URL_PATH);

// On récupère le chemin de base du site (ex: /dsm ou vide)
$site_base = parse_url(SITE_URL, PHP_URL_PATH) ?? '';
$admin_base = rtrim($site_base . '/admin', '/');

// Enlever le préfixe dynamique /base/admin
$path = preg_replace('#^' . preg_quote($admin_base, '#') . '#', '', $path);
// Normaliser le chemin (enlever le / initial et final)
$path = trim($path, '/');

// Table de routage : URL propre → fichier PHP
$routes = [
    ''                     => 'login.php',
    'connexion'            => 'login.php',
    'tableau-de-bord'      => 'dashboard.php',
    'actualites'           => 'news.php',
    'actualites/modifier'  => 'news_edit.php',
    'projets'              => 'projects.php',
    'projets/modifier'     => 'projects_edit.php',
    'commentaires'         => 'comments.php',
    'messages'             => 'messages.php',
    'profil'               => 'profile.php',
    'newsletter'           => 'newsletter.php',
    'newsletter/envoyer'   => 'newsletter_send.php',
    'parametres'           => 'settings.php',
    'utilisateurs'         => 'editors.php',
    'logs'                 => 'logs.php',
    'adhesions'            => 'adhesions.php',
    'adhesion-detail'      => 'adhesion_view.php',
    'adhesion-export'      => 'adhesion_export.php',
    'adhesion-download'    => 'adhesion_download_pdf.php',
    'forgot-password'      => 'forgot-password.php',
    'ads'                  => 'ads.php',
    'ads-edit'             => 'ads_edit.php',
    'deconnexion'          => 'logout.php',
];

// Résoudre le fichier correspondant
$target_file = $routes[$path] ?? null;
$id_from_path = null;

// Gestion des routes avec ID (ex: actualites/modifier/HASH)
if (!$target_file) {
    // On vérifie si le chemin correspond à [route/modifier]/[hash]
    if (preg_match('#^(.+/modifier)/(.+)$#', $path, $matches)) {
        $parent_route = $matches[1];
        $id_from_path = $matches[2];
        if (isset($routes[$parent_route])) {
            $target_file = $routes[$parent_route];
            // On injecte l'ID dans $_GET pour que les fichiers existants le trouvent s'ils cherchent 'id'
            $_GET['id'] = $id_from_path;
        }
    }
}

if ($target_file && file_exists(__DIR__ . '/' . $target_file)) {
    // Changer le répertoire courant vers admin/ pour que les includes relatifs fonctionnent
    chdir(__DIR__);
    require __DIR__ . '/' . $target_file;
} else {
    // 404 Admin
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404 - Admin DSM</title></head>';
    echo '<body style="font-family:sans-serif;text-align:center;padding:80px;">';
    echo '<h1 style="color:#e11d48">Page admin introuvable</h1>';
    echo '<p><a href="' . SITE_URL . '/admin">Retour au panneau d\'administration</a></p>';
    echo '</body></html>';
}
