<?php

/**
 * Retourne une chaîne relative "il y a X secondes/minutes/heures/jours..."
 * basée sur le datetime de publication ($datetime est la valeur BDD, ex: "2026-04-28 12:00:00").
 * Le calcul est fait par rapport à l'heure RÉELLE du serveur au moment de la requête.
 */
function timeAgo($datetime) {
    $now  = new DateTime('now');
    $pub  = new DateTime($datetime);
    $diff = $now->diff($pub);

    // Total en secondes pour une logique précise
    $totalSeconds = (int)($now->getTimestamp() - $pub->getTimestamp());

    if ($totalSeconds < 60) {
        return "à l'instant";
    } elseif ($totalSeconds < 3600) {
        $m = (int)floor($totalSeconds / 60);
        return "il y a " . $m . " " . ($m > 1 ? "minutes" : "minute");
    } elseif ($totalSeconds < 86400) {
        $h = (int)floor($totalSeconds / 3600);
        return "il y a " . $h . " " . ($h > 1 ? "heures" : "heure");
    } elseif ($totalSeconds < 604800) { // 7 jours
        $d = (int)floor($totalSeconds / 86400);
        return "il y a " . $d . " " . ($d > 1 ? "jours" : "jour");
    } else {
        // Plus d'une semaine → date complète lisible
        return formatSmartDate($datetime, false);
    }
}

/**
 * Affiche la date de publication de façon intelligente :
 * - Si l'article a moins de 24h : format relatif "il y a X min/h"
 * - Sinon : date complète FR avec heure si $withTime = true
 *
 * @param string $datetime  Valeur DATETIME de la BDD
 * @param bool   $withTime  Afficher l'heure en plus de la date (défaut : true)
 */
function formatSmartDate($datetime, $withTime = true) {
    $months = [
        'January' => 'janvier', 'February' => 'février', 'March' => 'mars',
        'April' => 'avril', 'May' => 'mai', 'June' => 'juin',
        'July' => 'juillet', 'August' => 'août', 'September' => 'septembre',
        'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'
    ];

    $ts  = strtotime($datetime);
    $now = time();
    $age = $now - $ts;

    // Moins de 24h → relatif
    if ($age < 86400) {
        return timeAgo($datetime);
    }

    // Plus d'un jour → date FR
    $formatted = date('d F Y', $ts);
    foreach ($months as $en => $fr) {
        $formatted = str_replace($en, $fr, $formatted);
    }
    if ($withTime) {
        $formatted .= ' à ' . date('H:i', $ts);
    }
    return $formatted;
}

/**
 * Convertit un lien brut (Youtube, X/Twitter, LinkedIn) en code Intégrable (iframe/embed)
 * S'il s'agit déjà d'un iframe complet, il est retourné tel quel.
 */
function parseEmbedCode($code) {
    if (empty(trim($code))) {
        return "";
    }

    $code = trim($code);

    // S'il s'agit déjà d'un code iframe copié-collé
    if (strpos(strtolower($code), '<iframe') !== false || strpos(strtolower($code), '<blockquote') !== false) {
        return $code;
    }

    // YouTube URL to iFrame
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $code, $id) || preg_match('/youtu\.be\/([^\&\?\/]+)/', $code, $id)) {
        return '<div class="embed-container" style="width: 100%; border-radius: 8px; overflow: hidden; background: #000;">
                    <iframe class="w-full aspect-video block" src="https://www.youtube.com/embed/'.$id[1].'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>';
    }

    // X (Twitter) URL to Embed
    if (strpos($code, 'twitter.com') !== false || strpos($code, 'x.com') !== false) {
        $twitter_url = str_replace('x.com', 'twitter.com', $code);
        return '<div class="x-embed-wrapper" style="margin: 2.5rem 0; display: flex; justify-content: center; width: 100%;">
                    <div style="width: 100%; max-width: 550px; background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden;">
                        <blockquote class="twitter-tweet" data-dnt="true" data-align="center"><a href="'.$twitter_url.'"></a></blockquote>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </div>
                </div>';
    }

    // LinkedIn URL 
    if (strpos($code, 'linkedin.com') !== false) {
        $embed_url = $code;
        $original_url = $code;

        // 1. Détection des URLs /posts/ ou /feed/ avec ID d'activité
        if (preg_match('/(?:activity|ugcPost|share|posts)-([0-9]{18,20})/', $code, $matches)) {
            $post_id = $matches[1];
            // On utilise systématiquement activity qui est le plus compatible
            $embed_url = "https://www.linkedin.com/embed/feed/update/urn:li:activity:" . $post_id;
        } 
        // 2. Détection déjà formatée /feed/update/urn...
        elseif (strpos($code, 'embed') === false && strpos($code, '/feed/update/') !== false) {
            $embed_url = str_replace('linkedin.com/feed/update/', 'linkedin.com/embed/feed/update/', $code);
        }

        return '<div class="linkedin-embed-wrapper" style="margin: 3rem 0; display: flex; flex-direction: column; align-items: center; width: 100%;">
                    <div style="width: 100%; max-width: 600px; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 30px rgba(0,0,0,0.06); overflow: hidden; position: relative;">
                        <!-- Loader discret -->
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); z-index: 0; color: #0a66c2; opacity: 0.3;">
                            <svg class="animate-spin" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                        </div>
                        <iframe src="'.$embed_url.'" height="750" width="100%" frameborder="0" allowfullscreen="" title="Embedded post" style="display: block; position: relative; z-index: 1;"></iframe>
                    </div>
                    <a href="'.$original_url.'" target="_blank" style="margin-top: 1rem; color: #0a66c2; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        Voir sur LinkedIn
                    </a>
                </div>';
    }

    // Lien par défaut cliquable
    return '<div style="margin: 1.5rem 0; padding: 1.5rem; background: #fff5f5; border-left: 4px solid #b91c1c; border-radius: 4px;">
                <a href="'.$code.'" target="_blank" style="color:#b91c1c; text-decoration:none; font-weight:700; font-family:\'Inter\',sans-serif; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Voir la publication externe
                </a>
            </div>';
}

/**
 * Génère un NanoID sécurisé (alphanumérique, chiffres + lettres min/maj)
 * Identique au standard NanoID (ex: V1StGq8YU5)
 *
 * @param int $length Longueur de l'ID (défaut 10)
 */
function generateNanoId($length = 10) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $id;
}

/**
 * Génère l'URL publique d'un enregistrement.
 *
 * Nouveau système : utilise le nanoid stocké en BDD.
 * Rétrocompatibilité : si le nanoid n'est pas fourni, on encode l'entier (legacy).
 *
 * Appel recommandé : getUrl('news', $news['id'], $news['slug'], $news['nanoid'])
 * Appel court (legacy) : getUrl('news', $id, $slug)
 *
 * @param string      $table   'news' ou 'projects'
 * @param int         $id      ID entier (pour les URLs admin et le fallback)
 * @param string|null $slug    Slug de l'article
 * @param string|null $nanoid  NanoID stocké en BDD (prioritaire)
 */
function getUrl($table, $id, $slug = null, $nanoid = null) {
    global $hashids;

    // Utiliser le nanoid s'il est fourni (nouveau système)
    if (!empty($nanoid)) {
        $token = $nanoid;
    } else {
        // Fallback legacy : encode l'integer avec hashids
        $token = $hashids->encode($id);
    }

    // Nettoyer le slug
    if (!empty($slug)) {
        $slug = generateSlug($slug);
    } else {
        $slug = '-';
    }

    return SITE_URL . "/view/" . $table . "/" . $token . "/" . $slug;
}

// Helper pour forcer la redirection si l'URL courante ne correspond pas au slug canonique
function forceCanonicalUrl($table, $id, $canonicalSlug, $nanoid = null) {
    $currentUri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $expectedUrl = getUrl($table, $id, $canonicalSlug, $nanoid);
    $expectedUri = parse_url($expectedUrl, PHP_URL_PATH);

    if ($currentUri !== $expectedUri) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $expectedUrl);
        exit();
    }
}

/**
 * Envoie un email via la fonction native mail()
 * Supprime les warnings si SMTP n'est pas configuré pour éviter de bloquer l'application
 */
function sendMail($to, $subject, $body) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>' . "\r\n";

    try {
        // Le @ supprime l'erreur si le serveur SMTP local n'est pas configuré (ex: XAMPP par défaut)
        return @mail($to, $subject, $body, $headers);
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : " . $e->getMessage());
        return false;
    } catch (Error $e) {
        error_log("Erreur système d'envoi d'email : " . $e->getMessage());
        return false;
    }
}

/**
 * Calcule le temps de lecture estimé d'un contenu (mots + images + vidéos)
 * Basé sur une vitesse de 180 mots/min et le temps visuel des médias.
 */
function getReadingTime($content) {
    if (empty($content)) return 1;

    // 1. Nettoyage et compte des mots compatible UTF-8 (Supporte les accents français)
    $cleanContent = strip_tags($content);
    preg_match_all('~[\p{L}\p{N}]+~u', $cleanContent, $m);
    $words = count($m[0]);
    
    // Vitesse moyenne : 180 mots/minute (standard pour lecture attentive)
    $seconds = ($words / 180) * 60;

    // 2. Calcul basé sur les images (Algorithm Medium)
    preg_match_all('/<img/i', $content, $matches);
    $imageCount = count($matches[0]);
    $imageTime = 0;
    $extraTime = 12; 
    for ($i = 1; $i <= $imageCount; $i++) {
        $imageTime += $extraTime;
        if ($extraTime > 3) $extraTime--;
    }

    // 3. Calcul pour les intégrations vidéos/iframes (env. 30s par média pour l'interaction)
    preg_match_all('/<(iframe|blockquote|video)/i', $content, $matchesMedia);
    $mediaCount = count($matchesMedia[0]);
    $mediaTime = $mediaCount * 30; 
    
    $totalSeconds = $seconds + $imageTime + $mediaTime;
    
    // Ajout d'un délai fixe pour le titre, le chapô et l'analyse visuelle (header/auteur)
    $totalSeconds += 15;

    $minutes = ceil($totalSeconds / 60);

    // Retourne au moins 1 minute
    return (int)($minutes > 0 ? $minutes : 1);
}

/**
 * Envoie une notification Push aux abonnés PWA
 * Nécessite l'installation de 'minishlink/web-push' pour fonctionner réellement.
 */
function sendPushNotification($title, $body, $url = '/') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM push_subscriptions");
        $subscriptions = $stmt->fetchAll();

        if (empty($subscriptions)) return true;

        // Note: L'envoi réel nécessite une clé VAPID et la librairie web-push.
        // Voici la structure logique :
        /*
        foreach ($subscriptions as $sub) {
            // Envoyer via WebPush
        }
        */
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur Push : " . $e->getMessage());
        return false;
    }
}
