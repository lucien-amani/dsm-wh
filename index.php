<?php
// Charger Composer
require_once 'vendor/autoload.php';

// Charger la configuration et les helpers
require_once 'config/database.php';
require_once 'config/mail.php';
require_once 'config/router.php'; // inclut autoload de AltoRouter et Hashids
// helpers.php est chargé via composer autoload (files), mais au cas où :
if (!function_exists('getUrl')) {
    require_once 'config/helpers.php';
}

// Essayer de faire correspondre la requête actuelle
$match = $router->match();

// Routage
if ($match && is_callable($match['target'])) {
    
    // Si la cible est une fonction anonyme (closure), on l'exécute
    $result = call_user_func_array($match['target'], $match['params']);
    
    // Si la closure retourne un tableau (pour nos routes dynamiques), on extrait les variables
    if (is_array($result) && isset($result['target'])) {
        
        // Variables disponibles pour la vue :
        $id = $result['id'] ?? null;
        $original_slug = $result['original_slug'] ?? null;
        
        // Inclure le fichier de vue
        if (file_exists($result['target'])) {
            require $result['target'];
        } else {
            // Fichier de vue manquant malgré route valide
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            echo "Erreur 404 : Vue introuvable.";
            // include 'pages/404.php';
        }
    }

} elseif ($match && is_string($match['target'])) {
    
    // Si la cible est une chaîne (ex: 'index', 'contact'), on cherche un fichier correspondant
    
    // Mapping des noms de cibles vers des fichiers réels
    $staticPages = [
        'index' => 'pages/home.php',
        'contact' => 'pages/contact.php',
        'about' => 'pages/about.php',
        'mentions-legales' => 'pages/mentions-legales.php',
        'politique-confidentialite' => 'pages/politique-confidentialite.php',
        'cookies' => 'pages/cookies.php',
        'news' => 'pages/news.php',
        'projects' => 'pages/projects.php',
        'sitemap' => 'sitemap.php',
        'robots' => 'robots.php'
    ];

    if (array_key_exists($match['target'], $staticPages)) {
        require $staticPages[$match['target']];
    } else {
        // Fallback générique si on a oublié de mapper
        // On suppose que le target est le nom du fichier dans pages/
        $file = 'pages/' . $match['target'] . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            echo "Erreur 404 : Page non trouvée.";
        }
    }

} else {
    // Pas de match trouvé -> 404
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "<h1>Erreur 404</h1><p>La page demandée n'existe pas.</p>";
    // include 'pages/404.php';
}
