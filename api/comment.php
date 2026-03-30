<?php
require_once __DIR__ . '/../config/database.php';
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'admin@dsm-samy.com');
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$news_id    = trim($_POST['news_id']    ?? '');
$news_title = trim($_POST['news_title'] ?? 'un article');
$user_name  = trim($_POST['user_name']  ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$content    = trim($_POST['content']    ?? '');

// Validation de base
if (!$news_id || !$user_name || !$content) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

if (strlen($content) < 10) {
    echo json_encode(['success' => false, 'message' => 'Votre commentaire est trop court (minimum 10 caractères).']);
    exit;
}

if ($user_email && !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'L\'adresse email saisie n\'est pas valide.']);
    exit;
}

$pdo = getDBConnection();

// Anti-spam : même contenu, même auteur (Session)
$hash = md5($news_id . $user_name . $content);
if (isset($_SESSION['last_comment_hash']) && $_SESSION['last_comment_hash'] === $hash) {
    echo json_encode(['success' => false, 'message' => 'Ce commentaire a déjà été envoyé.']);
    exit;
}

// Anti-spam : même contenu, même auteur (Base de données)
$stmt_check = $pdo->prepare("SELECT id FROM comments WHERE news_id = ? AND user_name = ? AND content = ? LIMIT 1");
$stmt_check->execute([$news_id, $user_name, $content]);
if ($stmt_check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ce commentaire existe déjà pour cet article.']);
    exit;
}

// Anti-spam basic : délai entre deux commentaires (30s)
if (isset($_SESSION['last_comment_time']) && (time() - $_SESSION['last_comment_time']) < 30) {
    $wait = 30 - (time() - $_SESSION['last_comment_time']);
    echo json_encode(['success' => false, 'message' => "Veuillez attendre {$wait} secondes avant de commenter à nouveau."]);
    exit;
}

try {
    // Check if user_email column exists in comments table
    $cols = $pdo->query("SHOW COLUMNS FROM comments LIKE 'user_email'")->fetch();
    if ($cols) {
        $stmt = $pdo->prepare("
            INSERT INTO comments (news_id, user_name, user_email, content, status, created_at)
            VALUES (:news_id, :user_name, :user_email, :content, 'pending', NOW())
        ");
        $stmt->execute([':news_id' => $news_id, ':user_name' => $user_name, ':user_email' => $user_email ?: null, ':content' => $content]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO comments (news_id, user_name, content, status, created_at)
            VALUES (:news_id, :user_name, :content, 'pending', NOW())
        ");
        $stmt->execute([':news_id' => $news_id, ':user_name' => $user_name, ':content' => $content]);
    }

    // Sauvegarder en session
    $_SESSION['last_comment_hash'] = $hash;
    $_SESSION['last_comment_time'] = time();
    $_SESSION['last_comment_name'] = $user_name;

    // -----------------------------------------------------------------
    // NOTIFICATION EMAIL
    // -----------------------------------------------------------------
    $siteUrl   = defined('SITE_URL')  ? SITE_URL  : 'http://localhost/dsm';
    $siteName  = defined('SITE_NAME') ? SITE_NAME : 'DSM';
    $adminMail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@dsm.com';

    $articleUrl = $siteUrl . '/view/news/' . $news_id . '/' . slugify($news_title ?? 'article');

    // -- Mail à l'utilisateur (si email fourni) --
    if ($user_email) {
        $subjectUser = "✅ Votre commentaire a bien été reçu – {$siteName}";
        $bodyUser = "
<!DOCTYPE html>
<html lang='fr'>
<head><meta charset='UTF-8'><style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#f8fafc;margin:0;padding:0}
  .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,.08)}
  .header{background:linear-gradient(135deg,#0f172a,#1e3a5f);padding:36px 32px;text-align:center}
  .header h1{color:#fff;margin:0;font-size:22px;font-weight:800;letter-spacing:-.5px}
  .header p{color:rgba(255,255,255,.6);margin:6px 0 0;font-size:13px}
  .body{padding:32px}
  .bubble{background:#f1f5f9;border-radius:16px;padding:20px;border-left:4px solid #3b82f6;font-size:15px;color:#374151;line-height:1.6;font-style:italic}
  .info{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-top:20px;font-size:13px;color:#166534}
  .btn{display:inline-block;margin-top:24px;padding:14px 28px;background:#2563eb;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px}
  .footer{text-align:center;padding:20px 32px;font-size:12px;color:#9ca3af;border-top:1px solid #f3f4f6}
</style></head>
<body>
<div class='wrap'>
  <div class='header'>
    <h1>💬 Commentaire reçu !</h1>
    <p>{$siteName} – Fil de commentaires</p>
  </div>
  <div class='body'>
    <p style='color:#374151;font-size:15px'>Bonjour <strong>" . htmlspecialchars($user_name) . "</strong>,</p>
    <p style='color:#6b7280;font-size:14px;margin-bottom:16px'>Merci pour votre commentaire sur l'article <strong>« " . htmlspecialchars($news_title) . " »</strong>. Voici ce que vous avez écrit :</p>
    <div class='bubble'>
      " . nl2br(htmlspecialchars($content)) . "
    </div>
    <div class='info'>
      ✅ Votre commentaire a bien été reçu et sera affiché après validation par notre équipe (généralement sous 24h).
    </div>
    <a href='{$articleUrl}' class='btn'>Voir l'article →</a>
  </div>
  <div class='footer'>{$siteName} — Cet email a été envoyé automatiquement suite à votre commentaire.</div>
</div>
</body></html>";

        $headersUser  = "MIME-Version: 1.0\r\n";
        $headersUser .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headersUser .= "From: {$siteName} <{$adminMail}>\r\n";
        $headersUser .= "Reply-To: {$adminMail}\r\n";
        $headersUser .= "X-Mailer: PHP/" . phpversion();

        @mail($user_email, $subjectUser, $bodyUser, $headersUser);
    }

    // -- Notification à l'admin --
    $subjectAdmin = "🔔 Nouveau commentaire en attente – " . htmlspecialchars($news_title);
    $bodyAdmin = "
<!DOCTYPE html>
<html lang='fr'>
<head><meta charset='UTF-8'><style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#f8fafc;margin:0;padding:0}
  .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,.08)}
  .header{background:linear-gradient(135deg,#0f172a,#1e3a5f);padding:30px 32px;text-align:center}
  .header h1{color:#fff;margin:0;font-size:20px;font-weight:800}
  .body{padding:28px 32px}
  .meta{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
  .row{display:flex;gap:8px;font-size:14px}
  .label{font-weight:700;color:#374151;min-width:90px}
  .val{color:#6b7280}
  .bubble{background:#fafafa;border:1px solid #e5e7eb;border-radius:14px;padding:18px;font-size:15px;color:#374151;line-height:1.6}
  .btn{display:inline-block;margin-top:20px;padding:12px 24px;background:#16a34a;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px}
  .footer{text-align:center;padding:16px 32px;font-size:12px;color:#9ca3af;border-top:1px solid #f3f4f6}
</style></head>
<body>
<div class='wrap'>
  <div class='header'><h1>🔔 Nouveau commentaire à modérer</h1></div>
  <div class='body'>
    <div class='meta'>
      <div class='row'><span class='label'>Article :</span><span class='val'>" . htmlspecialchars($news_title) . "</span></div>
      <div class='row'><span class='label'>Auteur :</span><span class='val'>" . htmlspecialchars($user_name) . "</span></div>
      <div class='row'><span class='label'>Email :</span><span class='val'>" . ($user_email ? htmlspecialchars($user_email) : '<em>non fourni</em>') . "</span></div>
      <div class='row'><span class='label'>Date :</span><span class='val'>" . date('d/m/Y \à H:i') . "</span></div>
    </div>
    <div class='bubble'>" . nl2br(htmlspecialchars($content)) . "</div>
    <a href='{$siteUrl}/admin' class='btn'>Modérer dans l'admin →</a>
  </div>
  <div class='footer'>{$siteName} – Notification automatique</div>
</div>
</body></html>";

    $headersAdmin  = "MIME-Version: 1.0\r\n";
    $headersAdmin .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headersAdmin .= "From: {$siteName} <{$adminMail}>\r\n";
    $headersAdmin .= "X-Mailer: PHP/" . phpversion();

    @mail($adminMail, $subjectAdmin, $bodyAdmin, $headersAdmin);

    echo json_encode([
        'success' => true,
        'message' => $user_email
            ? 'Commentaire envoyé ! Vous recevrez un email de confirmation dès validation.'
            : 'Commentaire envoyé avec succès. Il sera publié après modération.'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}

// Helper slug si non défini
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
?>
