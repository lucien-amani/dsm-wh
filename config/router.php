<?php
use Hashids\Hashids;

// Initialiser AltoRouter
$router = new AltoRouter();

// Définir le chemin de base (sous-dossier actuel)
// Si votre site est à la racine, laissez vide ou '/'. 
// Ici on détecte dynamiquement "/dsm"
$basePath = parse_url(SITE_URL, PHP_URL_PATH);
$router->setBasePath($basePath);

// Initialiser Hashids
// SALT : Changez cette chaîne pour rendre vos IDs uniques à votre projet
// MIN_LENGTH : 8 caractères minimum pour l'ID chiffré
// ALPHABET : Caractères utilisés (pas de confusion comme 0/O, 1/l)
$hashids = new Hashids(
    'DSM_SECURE_K3y_9z7x4v2b1n6m8QW3R5T7Y9U2I4O6P8A0S2D4F6G8H0J2K4L6Z8X0C2V4B6N8M', 
    8, 
    'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'
);

// --------------------------------------------------------------
// DÉFINITION DES ROUTES
// --------------------------------------------------------------

// 1. Page d'accueil
$router->map('GET', '/', 'index', 'home');

// 2. Routes statiques simples (pages directes)
$router->map('GET', '/contact', 'contact', 'contact');
$router->map('GET', '/a-propos', 'about', 'about');
$router->map('GET', '/mentions-legales', 'mentions-legales', 'mentions-legales');
$router->map('GET', '/politique-confidentialite', 'politique-confidentialite', 'privacy');
$router->map('GET', '/cookies', 'cookies', 'cookies');

// SEO Routes
$router->map('GET', '/sitemap.xml', 'sitemap', 'sitemap');
$router->map('GET', '/robots.txt', 'robots', 'robots');

// 3. Listes (avec pagination optionnelle ou filtres)
// Ex: /actualites, /projets
$router->map('GET', '/actualites', 'news', 'news_list');
$router->map('GET', '/projets', 'projects', 'projects_list');

// 4. ROUTE GÉNÉRIQUE DYNAMIQUE pour les détails
// Structure : /view/[table]/[hash_id]/[slug]
// Ex: /view/news/XYZ123/titre-de-l-article
$router->map('GET', '/view/[a:table]/[a:hash]/[*:slug]?', function($table, $hash, $slug = null) use ($hashids) {
    
    // Tentative de décodage de l'ID
    $ids = $hashids->decode($hash);
    
    if (empty($ids)) {
        return false; // ID invalide -> 404
    }
    
    $id = $ids[0];
    
    // Mapping des tables vers les fichiers de vue
    // Permet de sécuriser et de rediriger vers le bon fichier template
    $routesMap = [
        'news' => 'pages/news/detail.php',
        'projects' => 'pages/projects/detail.php',
        // Ajoutez d'autres tables ici au besoin
    ];

    if (!array_key_exists($table, $routesMap)) {
        return false; // Table non autorisée -> 404
    }

    // Retourne le fichier à inclure et l'ID décodé
    return [
        'target' => $routesMap[$table],
        'id' => $id,
        'original_slug' => $slug
    ];

}, 'dynamic_route');
