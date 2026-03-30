<?php

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

// Fonction pour générer une URL chiffrée
function getUrl($table, $id, $slug = null) {
    global $router, $hashids;

    // Chiffrer l'ID
    $hash = $hashids->encode($id);
    
    // Si un slug est fourni, on s'assure qu'il est propre
    if (!empty($slug)) {
        $slug = generateSlug($slug); // utilise la fonction existante
    } else {
        $slug = $slug ?? '-'; // cas où il manque un slug
    }

    // Retourner l'URL formatée à partir de la route nommée
    // Modèle : /view/[table]/[hash]/[slug]
    return SITE_URL . "/view/" . $table . "/" . $hash . "/" . $slug;
}

// Fonction pour décoder une URL "chiffrée" (inverse de getUrl)
// Utile si vous avez besoin de récupérer l'ID depuis une chaîne hash manuellement
function decodeUrlHash($hash) {
    global $hashids;
    $ids = $hashids->decode($hash);
    return !empty($ids) ? $ids[0] : null;
}

// Helper pour forcer la redirection si l'URL courante ne correspond pas au slug canonique
// (Évite le duplicate content si l'utilisateur change manuellement l'URL)
function forceCanonicalUrl($table, $id, $canonicalSlug) {
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $expectedUrl = getUrl($table, $id, $canonicalSlug);
    $expectedUri = parse_url($expectedUrl, PHP_URL_PATH);
    
    if ($currentUri !== $expectedUri) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $expectedUrl);
        exit();
    }
}

/**
 * Envoie un email via PHPMailer
 */
function sendMail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Paramètres du serveur
        $mail->isSMTP();
        $mail->CharSet    = 'UTF-8';
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = MAIL_AUTH;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = MAIL_ENCRYPTION === 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = MAIL_PORT;

        // Destinataires
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($to);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : " . $mail->ErrorInfo);
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
