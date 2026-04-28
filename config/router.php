<?php
require_once __DIR__ . '/no-composer-lib.php';

// Initialiser le Router
$router = new SimpleRouter();

// Définir le chemin de base
$basePath = parse_url(SITE_URL, PHP_URL_PATH);
$router->setBasePath($basePath);

$hashids = new SimpleHashids(
    'DSM_SECURE_K3y_9z7x4v2b1n6m8QW3R5T7Y9U2I4O6P8A0S2D4F6G8H0J2K4L6Z8X0C2V4B6N8M', 
    8, 
    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'
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

    // Mapping des tables autorisées vers les fichiers de vue
    $routesMap = [
        'news'     => 'pages/news/detail.php',
        'projects' => 'pages/projects/detail.php',
    ];

    if (!array_key_exists($table, $routesMap)) {
        return false; // Table non autorisée → 404
    }

    // --- Résolution de l'ID ---
    // 1. Essayer en premier : lookup nanoid en BDD (nouveau système)
    try {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM `{$table}` WHERE nanoid = ? LIMIT 1");
        $stmt->execute([$hash]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return [
                'target'        => $routesMap[$table],
                'id'            => (int)$row['id'],
                'original_slug' => $slug
            ];
        }
    } catch (Exception $e) { /* BDD non dispo : on tente le fallback */ }

    // 2. Fallback : décodage hashids (rétrocompatibilité avec les anciennes URLs)
    $ids = $hashids->decode($hash);
    if (!empty($ids)) {
        return [
            'target'        => $routesMap[$table],
            'id'            => $ids[0],
            'original_slug' => $slug
        ];
    }

    return false; // Aucune correspondance → 404

}, 'dynamic_route');
