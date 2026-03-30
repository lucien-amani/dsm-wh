<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($id)) {
    header('Location: ' . SITE_URL . '/news.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name, u.avatar as author_avatar FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.id = :id AND n.published = 1");
$stmt->execute([':id' => $id]);
$news = $stmt->fetch();

if (!$news) {
    header("HTTP/1.0 404 Not Found");
    echo "Article introuvable";
    exit;
}

if (isset($original_slug) && $original_slug !== $news['slug']) {
    forceCanonicalUrl('news', $id, $news['slug']);
}

$stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $id]);

$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id AND category = :category ORDER BY created_at DESC LIMIT 6");
$stmt->execute([':id' => $news['id'], ':category' => $news['category']]);
$category_news = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id ORDER BY created_at DESC LIMIT 6");
$stmt->execute([':id' => $news['id']]);
$recent_news = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id ORDER BY views DESC LIMIT 4");
$stmt->execute([':id' => $news['id']]);
$popular_news = $stmt->fetchAll();

$current_page = 'news';
$page_title = htmlspecialchars($news['title']) . ' - Dynamique Samy Magadju/Wema ni Hakiba';
$page_description = htmlspecialchars($news['excerpt']);
$page_image = $news['image'] ? (strpos($news['image'], 'http') === 0 ? $news['image'] : SITE_URL . '/uploads/' . $news['image']) : SITE_URL . '/assets/logo/logo-dsm.jpg';
$read_time = getReadingTime($news['content']);

// Variables SEO pour JSON-LD
$is_article = true;
$article_title = $news['title'];
$article_date = date('c', strtotime($news['created_at']));
$article_author = $news['author_name'];

include 'includes/header.php';
?>

<!-- Progress Bar -->
<div id="reading-progress" style="position:fixed;top:0;left:0;height:3px;background:#b91c1c;z-index:9999;width:0;transition:width 0.15s;"></div>

<style>
/* Layout principal */
.detail-wrap { max-width: 1200px; margin: 0 auto; padding: 2.5rem 1rem 4rem; }
.detail-grid { display: flex; gap: 3rem; align-items: flex-start; }
.detail-main { flex: 1; min-width: 0; }
.detail-sidebar { width: 300px; flex-shrink: 0; }

@media (max-width: 900px) {
    .detail-grid { flex-direction: column; }
    .detail-sidebar { width: 100%; }
}

/* Article typography */
.article-category { font-size: 11px; font-weight: 900; letter-spacing: .2em; text-transform: uppercase; color: #b91c1c; font-family: 'Inter', sans-serif; margin-bottom: .5rem; }
.article-title { font-size: clamp(1.8rem, 4vw, 3.2rem); font-family: 'Playfair Display', serif; font-weight: 900; line-height: 1.1; color: #111; margin-bottom: 1.5rem; }
.dark .article-title { color: #fff; }
.article-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #888; font-family: 'Inter', sans-serif; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem; }
.dark .article-meta { border-color: rgba(255,255,255,.07); }
.article-meta-sep { color: #ddd; }
.article-img { width: 100%; display: block; max-height: 500px; object-fit: cover; }
.article-credit { text-align: right; font-size: 9px; text-transform: uppercase; letter-spacing: .2em; color: #aaa; font-family: 'Inter', sans-serif; font-weight: 700; margin: .25rem 0 1.5rem; }
.article-chapeau { font-size: 1.15rem; font-family: 'Playfair Display', serif; font-weight: 700; color: #1a6fb5; line-height: 1.7; margin-bottom: 2rem; }
.dark .article-chapeau { color: #60a5fa; }

/* Article body */
#article-body { font-family: 'Playfair Display', serif; font-size: 1.05rem; line-height: 1.85; color: #1a1a1a; }
.dark #article-body { color: #e2e8f0; }
#article-body p { margin-bottom: 1.4rem; }
#article-body p:first-of-type::first-letter { float:left; font-size:4.5rem; line-height:1; margin:5px 10px 0 0; font-weight:900; color:#b91c1c; font-family:'Playfair Display',serif; }
#article-body h2 { font-family:'Inter',sans-serif; font-weight:800; font-size:1.3rem; border-left:4px solid #b91c1c; padding-left:1rem; margin:2rem 0 1rem; }
#article-body h3 { font-family:'Inter',sans-serif; font-weight:700; margin:1.5rem 0 .75rem; }
#article-body blockquote { border-left:3px solid #b91c1c; padding-left:1.5rem; font-style:italic; color:#555; margin:1.5rem 0; }
.dark #article-body blockquote { color:#aaa; }
#article-body a { color:#1a6fb5; text-decoration:underline; }
#article-body img { max-width:100%; margin:1.5rem 0; }

/* Share bar */
.share-bar { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; padding:.9rem 0; border-top:1px solid #eee; border-bottom:1px solid #eee; margin-bottom:2.5rem; font-family:'Inter',sans-serif; }
.dark .share-bar { border-color:rgba(255,255,255,.07); }
.share-btn { display:flex; align-items:center; gap:.4rem; font-size:11px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; color:#555; background:none; border:none; cursor:pointer; padding:.3rem .5rem; transition:color .2s; }
.dark .share-btn { color:#aaa; }
.share-btn:hover { color:#b91c1c; }
.share-icons { display:flex; align-items:center; gap:.5rem; margin-left:auto; }
.share-icon { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#f1f1f1; transition:all .2s; }
.dark .share-icon { background:#334155; }
.share-icon:hover { transform:translateY(-2px); }

/* Linked articles 3-cols */
.linked-section { border-top: 2px solid #111; padding-top: 1.25rem; margin-bottom: 2.5rem; }
.dark .linked-section { border-color: #fff; }
.linked-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
@media(max-width:600px) { .linked-grid { grid-template-columns: 1fr; } }
.linked-item { display:flex; gap:.6rem; align-items:flex-start; text-decoration:none; }
.linked-badge { width:26px; height:26px; background:#b91c1c; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-weight:900; font-size:13px; flex-shrink:0; margin-top:2px; }
.linked-title { font-size:13px; font-weight:700; color:#111; line-height:1.35; font-family:'Inter',sans-serif; transition:color .2s; }
.dark .linked-title { color:#e2e8f0; }
.linked-item:hover .linked-title { color:#b91c1c; }

/* Grid d'images (à lire aussi) */
.grid-section { border-top: 1px solid #e5e7eb; padding-top: 1.5rem; margin-bottom: 2.5rem; }
.dark .grid-section { border-color:rgba(255,255,255,.07); }
.grid-label { font-size:10px; font-weight:900; letter-spacing:.3em; text-transform:uppercase; color:#888; font-family:'Inter',sans-serif; margin-bottom:1rem; }
.img-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; }
@media(max-width:600px) { .img-grid { grid-template-columns: repeat(2,1fr); } }
.img-grid-item { text-decoration:none; display:block; }
.img-grid-thumb { aspect-ratio:4/3; overflow:hidden; background:#f1f1f1; }
.img-grid-thumb img { width:100%; height:100%; object-fit:cover; transition:transform .5s; }
.img-grid-item:hover .img-grid-thumb img { transform:scale(1.05); }
.img-grid-cat { font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; color:#b91c1c; font-family:'Inter',sans-serif; margin:.5rem 0 .2rem; display:block; }
.img-grid-ttl { font-size:12px; font-weight:700; color:#111; line-height:1.35; font-family:'Inter',sans-serif; display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.dark .img-grid-ttl { color:#e2e8f0; }
.img-grid-item:hover .img-grid-ttl { color:#b91c1c; }

/* Comments */
.comments-section { border-top:2px solid #111; padding-top:1.5rem; }
.dark .comments-section { border-color:#fff; }
.comments-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.comments-hdr h3 { font-size:14px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; color:#111; font-family:'Inter',sans-serif; }
.dark .comments-hdr h3 { color:#fff; }
.react-btn { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.15em; background:#b91c1c; color:#fff; border:none; cursor:pointer; padding:.6rem 1.2rem; transition:background .2s; font-family:'Inter',sans-serif; }
.react-btn:hover { background:#991b1b; }
.comment-item { display:flex; gap:1rem; padding:1.25rem 0; border-bottom:1px solid #f1f1f1; }
.dark .comment-item { border-color:rgba(255,255,255,.05); }
.comment-avatar { width:40px; height:40px; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:15px; flex-shrink:0; }
.comment-name { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; color:#111; font-family:'Inter',sans-serif; }
.dark .comment-name { color:#fff; }
.comment-date { font-size:10px; color:#aaa; font-family:'Inter',sans-serif; }
.comment-text { font-size:14px; font-family:'Playfair Display',serif; color:#333; line-height:1.7; font-style:italic; margin-top:.5rem; }
.dark .comment-text { color:#bbb; }
.no-comments { border:2px dashed #e5e7eb; text-align:center; padding:3rem 1rem; border-radius:.5rem; font-family:'Playfair Display',serif; font-style:italic; color:#999; }
.dark .no-comments { border-color:rgba(255,255,255,.07); }

/* SIDEBAR */
.sb-block { border:1px solid #e5e7eb; margin-bottom:1.5rem; overflow:hidden; }
.dark .sb-block { border-color:rgba(255,255,255,.07); }
.sb-img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
.sb-body { padding:1rem; }
.sb-teaser { font-size:13px; font-family:'Playfair Display',serif; color:#333; line-height:1.6; margin-bottom:.75rem; text-decoration:none; display:block; }
.dark .sb-teaser { color:#bbb; }
.sb-teaser:hover { color:#b91c1c; }
.sb-readtime { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; color:#b91c1c; font-family:'Inter',sans-serif; }

.sb-partners { background:#eef5fc; padding:1rem 1.25rem; margin-bottom:1.5rem; border:1px solid #c8dff0; }
.dark .sb-partners { background:rgba(30,58,138,.15); border-color:rgba(96,165,250,.15); }
.sb-partners-hdr { display:flex; align-items:center; gap:.5rem; margin-bottom:.75rem; }
.sb-partners-hdr .lbl1 { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; color:#1a6fb5; border-bottom:2px solid #1a6fb5; padding-bottom:2px; font-family:'Inter',sans-serif; }
.sb-partners-hdr .lbl2 { font-size:10px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.2em; font-family:'Inter',sans-serif; }
.sb-partners h5 { font-size:12px; font-weight:900; text-transform:uppercase; color:#111; font-family:'Inter',sans-serif; margin-bottom:.5rem; }
.dark .sb-partners h5 { color:#fff; }
.sb-partners p { font-size:12px; color:#555; line-height:1.55; margin-bottom:.75rem; font-family:'Inter',sans-serif; }
.dark .sb-partners p { color:#94a3b8; }
.sb-partners a { font-size:11px; font-weight:700; color:#111; font-family:'Inter',sans-serif; text-decoration:underline; }
.dark .sb-partners a { color:#e2e8f0; }

.sb-section-title { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; color:#111; font-family:'Inter',sans-serif; border-top:2px solid #111; padding-top:.75rem; margin-bottom:1rem; }
.dark .sb-section-title { color:#fff; border-color:#fff; }
.sb-news-item { display:flex; gap:.6rem; padding-bottom:.9rem; border-bottom:1px solid #f1f1f1; margin-bottom:.9rem; text-decoration:none; }
.dark .sb-news-item { border-color:rgba(255,255,255,.05); }
.sb-news-item:last-child { border:none; margin:0; padding:0; }
.sb-news-thumb { width:60px; height:60px; object-fit:cover; flex-shrink:0; }
.sb-news-time { font-size:9px; font-weight:900; color:#b91c1c; display:block; font-family:'Inter',sans-serif; letter-spacing:.15em; }
.sb-news-title { font-size:12px; font-weight:700; color:#111; line-height:1.35; font-family:'Inter',sans-serif; display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.dark .sb-news-title { color:#e2e8f0; }
.sb-news-item:hover .sb-news-title { color:#b91c1c; }

.sb-popular-item { display:flex; align-items:flex-start; gap:.75rem; margin-bottom:1rem; text-decoration:none; }
.sb-popular-num { font-size:2rem; font-family:'Playfair Display',serif; font-weight:900; color:#e5e7eb; line-height:1; flex-shrink:0; width:1.5rem; text-align:center; }
.dark .sb-popular-num { color:#334155; }
.sb-popular-title { font-size:12px; font-weight:700; color:#111; line-height:1.35; font-family:'Inter',sans-serif; }
.dark .sb-popular-title { color:#e2e8f0; }
.sb-popular-item:hover .sb-popular-title { color:#b91c1c; }

.sb-cta { display:block; text-align:center; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; border:2px solid #111; color:#111; padding:.7rem 1rem; font-family:'Inter',sans-serif; text-decoration:none; margin-top:1.5rem; transition:all .2s; }
.dark .sb-cta { border-color:#fff; color:#fff; }
.sb-cta:hover { background:#111; color:#fff; }
.dark .sb-cta:hover { background:#fff; color:#111; }

/* Modal */
.modal-wrap { display:none; position:fixed; inset:0; z-index:9000; align-items:flex-end; justify-content:center; padding:0; }
.modal-wrap.open { display:flex; }
@media(min-width:640px) { .modal-wrap.open { align-items:center; padding:1.5rem; } }
.modal-overlay { position:absolute; inset:0; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); }
.modal-panel { position:relative; width:100%; max-width:520px; background:#fff; box-shadow:0 20px 80px rgba(0,0,0,.3); z-index:10; }
.dark .modal-panel { background:#1e293b; }
#cmt-form-view { display:block; }
#cmt-success-view { display:none; }
.modal-hdr { display:flex; align-items:flex-start; justify-content:space-between; padding:1.5rem; border-bottom:1px solid #f1f1f1; gap:1rem; }
.dark .modal-hdr { border-color:rgba(255,255,255,.07); }
.modal-hdr h3 { font-size:17px; font-weight:900; text-transform:uppercase; letter-spacing:.05em; color:#111; font-family:'Inter',sans-serif; margin:0 0 2px; }
.dark .modal-hdr h3 { color:#fff; }
.modal-close { background:none; border:none; cursor:pointer; color:#999; padding:.25rem; flex-shrink:0; }
.modal-close:hover { color:#b91c1c; }
.modal-close:hover { color:#b91c1c; }
.modal-body { padding:1.5rem; }
.modal-textarea { width:100%; border:1px solid #e5e7eb; background:#f8fafc; padding:1rem; font-size:15px; font-family:'Playfair Display',serif; font-style:italic; resize:none; outline:none; color:#333; transition:border .2s; box-sizing:border-box; }
.dark .modal-textarea { border-color:#334155; background:#0f172a; color:#e2e8f0; }
.modal-textarea:focus { border-color:#b91c1c; }
.modal-inputs { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-top:.75rem; }
@media(max-width:440px) { .modal-inputs { grid-template-columns:1fr; } }
.modal-input { border:1px solid #e5e7eb; background:#f8fafc; padding:.75rem 1rem; font-size:13px; outline:none; width:100%; color:#333; transition:border .2s; box-sizing:border-box; font-family:'Inter',sans-serif; }
.dark .modal-input { border-color:#334155; background:#0f172a; color:#e2e8f0; }
.modal-input:focus { border-color:#b91c1c; }
.modal-submit { width:100%; margin-top:1rem; padding:1rem; background:#b91c1c; color:#fff; border:none; cursor:pointer; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; font-family:'Inter',sans-serif; transition:background .2s; }
.modal-submit:hover { background:#991b1b; }
.modal-submit:disabled { opacity:.6; cursor:not-allowed; }
.modal-note { text-align:center; font-size:9px; text-transform:uppercase; letter-spacing:.2em; color:#aaa; font-family:'Inter',sans-serif; margin-top:.75rem; }
.modal-success { display:none; padding:3rem 1.5rem; text-align:center; }
.modal-success h3 { font-size:24px; font-weight:900; text-transform:uppercase; color:#111; font-family:'Inter',sans-serif; margin-bottom:.75rem; }
.dark .modal-success h3 { color:#fff; }
.modal-success p { font-size:14px; font-family:'Playfair Display',serif; color:#555; line-height:1.6; margin-bottom:1.5rem; }
.modal-success-close { padding:.75rem 2rem; background:#111; color:#fff; border:none; cursor:pointer; font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.2em; font-family:'Inter',sans-serif; }
.dark .modal-success-close { background:#fff; color:#111; }
</style>

<div class="detail-wrap">

    <!-- BREADCRUMB -->
    <nav style="margin-bottom:1.5rem;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:#aaa;font-family:'Inter',sans-serif;">
        <a href="<?php echo $router->generate('home'); ?>" style="color:inherit;text-decoration:none;hover:color:#b91c1c;">Accueil</a>
        <span style="margin:0 .4rem;">›</span>
        <a href="<?php echo $router->generate('news_list'); ?>" style="color:inherit;text-decoration:none;">Actualités</a>
        <?php if ($news['category']): ?>
        <span style="margin:0 .4rem;">›</span>
        <span style="color:#b91c1c;"><?php echo htmlspecialchars(strtoupper($news['category'])); ?></span>
        <?php endif; ?>
    </nav>

    <div class="detail-grid">

        <!-- ============ MAIN ARTICLE ============ -->
        <main class="detail-main">

            <div class="article-category"><?php echo htmlspecialchars($news['category'] ?: 'Actualité'); ?></div>

            <h1 class="article-title"><?php echo htmlspecialchars($news['title']); ?></h1>

            <div class="article-meta">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <?php if(!empty($news['author_avatar'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/avatars/<?php echo $news['author_avatar']; ?>" style="width:24px;height:24px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:24px;height:24px;border-radius:50%;background:#f1f1f1;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;color:#888;">
                            <?php echo strtoupper(substr($news['author_name'] ?: 'R', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span><?php echo htmlspecialchars($news['author_name'] ?: 'La Rédaction'); ?></span>
                </div>
                <span class="article-meta-sep">|</span>
                <span><?php echo formatDateFR($news['created_at']); ?> à <?php echo date('H:i', strtotime($news['created_at'])); ?></span>
                <span class="article-meta-sep">|</span>
                <span><?php echo number_format($news['views'], 0, ',', ' '); ?> lectures</span>
            </div>

            <!-- MAIN IMAGE -->
            <?php if (!empty($news['embed_code'])): ?>
                <div style="width:100%;"><?php echo parseEmbedCode($news['embed_code']); ?></div>
            <?php else: ?>
                <?php $article_image = $news['image'] ? SITE_URL . '/uploads/' . $news['image'] : SITE_URL . '/uploads/profil.jpg'; ?>
                <img src="<?php echo $article_image; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="article-img">
            <?php endif; ?>
            <p class="article-credit"><?php echo htmlspecialchars(!empty($news['author']) ? strtoupper($news['author']) : 'DYNAMIQUE SAMY MAGADJU'); ?> / DSM MEDIA</p>

            <!-- CHAPEAU -->
            <p class="article-chapeau"><?php echo htmlspecialchars($news['excerpt']); ?></p>

            <!-- BODY -->
            <div id="article-body"><?php echo $news['content']; ?></div>

            <!-- SHARE BAR -->
            <div class="share-bar">
                <button onclick="likeItem('<?php echo $news['id']; ?>', 'news', this)" class="share-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span id="news-likes-count"><?php echo $news['likes'] ?? 0; ?></span> J'aime
                </button>
                <button onclick="openCommentModal()" class="share-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Commenter
                </button>
                <div class="share-icons">
                    <span style="font-size:10px;font-weight:900;letter-spacing:.15em;text-transform:uppercase;color:#aaa;font-family:'Inter',sans-serif;margin-right:.25rem;">Partager :</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-icon" style="color:#1877f2;" title="Facebook">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($news['title']); ?>" target="_blank" class="share-icon" style="color:#111;" title="X/Twitter">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($news['title'] . ' ' . SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-icon" style="color:#25d366;" title="WhatsApp">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </div>
            </div>

            <!-- 3-COL LINKED ARTICLES -->
            <?php
            $linked = [];
            $linked_seen = [];
            foreach (array_merge($category_news, $recent_news) as $la) {
                if (!in_array($la['id'], $linked_seen) && count($linked) < 3) {
                    $linked_seen[] = $la['id'];
                    $linked[] = $la;
                }
            }
            ?>
            <?php if (!empty($linked)): ?>
            <div class="linked-section">
                <div class="linked-grid">
                    <?php foreach ($linked as $item): ?>
                    <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>" class="linked-item">
                        <div class="linked-badge">D</div>
                        <span class="linked-title"><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- IMAGE GRID (À lire aussi) -->
            <?php
            $grid = [];
            $grid_seen = [];
            foreach (array_merge($popular_news, $recent_news) as $ga) {
                if (!in_array($ga['id'], $grid_seen) && count($grid) < 6) {
                    $grid_seen[] = $ga['id'];
                    $grid[] = $ga;
                }
            }
            ?>
            <?php if (!empty($grid)): ?>
            <div class="grid-section">
                <p class="grid-label">À lire aussi</p>
                <div class="img-grid">
                    <?php foreach ($grid as $item): ?>
                    <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>" class="img-grid-item">
                        <div class="img-grid-thumb">
                            <img src="<?php echo $item['image'] ? SITE_URL.'/uploads/'.$item['image'] : SITE_URL.'/uploads/profil.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <span class="img-grid-cat"><?php echo htmlspecialchars($item['category'] ?: 'Actualité'); ?></span>
                        <span class="img-grid-ttl"><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- COMMENTS -->
            <?php
            $stmt = $pdo->prepare("SELECT * FROM comments WHERE news_id = :id AND status = 'approved' ORDER BY created_at DESC");
            $stmt->execute([':id' => $news['id']]);
            $comments = $stmt->fetchAll();
            if (!function_exists('getAvatarColor')) {
                function getAvatarColor($name) {
                    $colors = ['#4f46e5','#dc2626','#059669','#d97706','#2563eb'];
                    return $colors[ord(strtoupper(substr($name,0,1))) % count($colors)];
                }
            }
            ?>
            <div class="comments-section">
                <div class="comments-hdr">
                    <h3>Réactions <span style="color:#ddd;font-weight:400;">(<?php echo count($comments); ?>)</span></h3>
                    <button class="react-btn" onclick="openCommentModal()">Réagir</button>
                </div>
                <?php if (empty($comments)): ?>
                    <div class="no-comments">Aucune réaction pour le moment. Soyez le premier !</div>
                <?php else: ?>
                    <?php foreach ($comments as $c): ?>
                    <div class="comment-item">
                        <div class="comment-avatar" style="background:<?php echo getAvatarColor($c['user_name']); ?>">
                            <?php echo strtoupper(substr($c['user_name'],0,1)); ?>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.25rem;">
                                <span class="comment-name"><?php echo htmlspecialchars($c['user_name']); ?></span>
                                <span class="comment-date">Il y a <?php echo timeElapsedString($c['created_at']); ?></span>
                                <button onclick="likeItem('<?php echo $c['id']; ?>','comment',this)" style="margin-left:auto;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:.3rem;font-size:11px;font-weight:700;color:#aaa;font-family:'Inter',sans-serif;" class="share-btn">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span class="count"><?php echo $c['likes'] ?? 0; ?></span>
                                </button>
                            </div>
                            <p class="comment-text">"<?php echo nl2br(htmlspecialchars($c['content'])); ?>"</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>

        <!-- ============ SIDEBAR ============ -->
        <aside class="detail-sidebar">

            <!-- Article mis en avant -->
            <?php $sb = !empty($category_news[0]) ? $category_news[0] : (!empty($recent_news[0]) ? $recent_news[0] : null); ?>
            <?php if ($sb): ?>
            <a href="<?php echo getUrl('news', $sb['id'], $sb['slug']); ?>" class="sb-block" style="display:block;text-decoration:none;">
                <img src="<?php echo $sb['image'] ? SITE_URL.'/uploads/'.$sb['image'] : SITE_URL.'/uploads/profil.jpg'; ?>" class="sb-img">
                <div class="sb-body">
                    <span class="sb-teaser"><?php echo htmlspecialchars($sb['title']); ?></span>
                    <span class="sb-readtime"><?php echo $read_time; ?> min de lecture</span>
                </div>
            </a>
            <?php endif; ?>

            <!-- Services Partenaires -->
            <div class="sb-partners">
                <div class="sb-partners-hdr">
                    <span class="lbl1">Services</span>
                    <span class="lbl2">Partenaires</span>
                </div>
                <h5>« DSM » POUR LE DÉVELOPPEMENT</h5>
                <p>En français. Suivez les projets structurants de la Dynamique Samy Magadju pour Bukavu et le Sud-Kivu.</p>
                <a href="<?php echo $router->generate('projects_list'); ?>">« DSM » en action dans votre province →</a>
            </div>

            <!-- Fil des infos -->
            <p class="sb-section-title">Le Fil des Infos</p>
            <?php foreach (array_slice($recent_news, 0, 5) as $item): ?>
            <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>" class="sb-news-item">
                <img src="<?php echo $item['image'] ? SITE_URL.'/uploads/'.$item['image'] : SITE_URL.'/uploads/profil.jpg'; ?>" class="sb-news-thumb">
                <div>
                    <span class="sb-news-time"><?php echo date('H:i', strtotime($item['created_at'])); ?></span>
                    <span class="sb-news-title"><?php echo htmlspecialchars($item['title']); ?></span>
                </div>
            </a>
            <?php endforeach; ?>

            <!-- Les plus lus -->
            <p class="sb-section-title" style="margin-top:1.5rem;">Les plus lus</p>
            <?php foreach ($popular_news as $i => $item): ?>
            <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>" class="sb-popular-item">
                <span class="sb-popular-num"><?php echo $i+1; ?></span>
                <span class="sb-popular-title"><?php echo htmlspecialchars($item['title']); ?></span>
            </a>
            <?php endforeach; ?>

            <a href="<?php echo $router->generate('news_list'); ?>" class="sb-cta">Toute l'actualité →</a>

        </aside>
    </div>
</div>

<!-- MODAL COMMENTAIRES -->
<div id="cmt-modal" class="modal-wrap" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeCommentModal()"></div>
    <div class="modal-panel">
        <div id="cmt-form-view">
            <div class="modal-hdr">
                <div>
                    <h3>Réagir à cet article</h3>
                    <p style="font-size:11px;color:#aaa;font-family:'Inter',sans-serif;margin-top:3px;"><?php echo htmlspecialchars(mb_strlen($news['title']) > 60 ? mb_substr($news['title'], 0, 60).'…' : $news['title']); ?></p>
                </div>
                <button class="modal-close" onclick="closeCommentModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div style="position:relative;">
                    <textarea id="cmt-content" class="modal-textarea" rows="4" maxlength="1000" placeholder="Partagez votre point de vue sur cet article..." oninput="document.getElementById('cmt-chars').textContent=this.value.length"></textarea>
                    <span id="cmt-chars" style="position:absolute;bottom:8px;right:12px;font-size:10px;color:#ccc;font-family:'Inter',sans-serif;">0</span>
                </div>
                <div class="modal-inputs">
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:#aaa;font-family:'Inter',sans-serif;">Nom *</label>
                        <input type="text" id="cmt-name" class="modal-input" placeholder="Ex: Jean Dupont">
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:#aaa;font-family:'Inter',sans-serif;">E-mail <span style="font-weight:400;">(privé, optionnel)</span></label>
                        <input type="email" id="cmt-email" class="modal-input" placeholder="votre@email.com">
                    </div>
                </div>
                <div id="cmt-error" style="display:none;background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;font-size:12px;padding:.6rem 1rem;margin-top:.75rem;border-radius:4px;font-family:'Inter',sans-serif;"></div>
                <button id="cmt-submit-btn" class="modal-submit" onclick="submitComment()" style="margin-top:1rem;">
                    <span id="cmt-btn-text">Envoyer ma réaction</span>
                </button>
                <p class="modal-note">Votre commentaire sera examiné par notre équipe avant publication. ✓</p>
            </div>
        </div>
        <div id="cmt-success-view" class="modal-success">
            <div style="width:64px;height:64px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg width="30" height="30" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3>Commentaire envoyé !</h3>
            <p>Merci pour votre participation. Votre commentaire est en cours de modération et sera publié prochainement si approuvé par notre équipe.</p>
            <button class="modal-success-close" onclick="closeCommentModal()">Fermer</button>
        </div>
    </div>
</div>

<script>
// PHP values safely assigned to JS variables
var DSM_NEWS_ID    = <?php echo json_encode((string)$news['id'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var DSM_NEWS_TITLE = <?php echo json_encode($news['title'],     JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var DSM_API_BASE   = <?php echo json_encode(SITE_URL,           JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

window.onscroll = function() {
    var s = document.body.scrollTop || document.documentElement.scrollTop;
    var h = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var bar = document.getElementById('reading-progress');
    if (bar) bar.style.width = (s / h * 100) + '%';
};

window.openCommentModal = function() {
    var formView    = document.getElementById('cmt-form-view');
    var successView = document.getElementById('cmt-success-view');
    var modal       = document.getElementById('cmt-modal');
    if (formView)    formView.style.display    = 'block';
    if (successView) successView.style.display = 'none';
    if (modal)       modal.classList.add('open');
    document.body.style.overflow = 'hidden';
};

window.closeCommentModal = function() {
    var modal = document.getElementById('cmt-modal');
    if (modal) modal.classList.remove('open');
    document.body.style.overflow = '';
};

window.submitComment = function() {
    var content = document.getElementById('cmt-content').value.trim();
    var name    = document.getElementById('cmt-name').value.trim();
    var email   = document.getElementById('cmt-email').value.trim();
    var btn     = document.getElementById('cmt-submit-btn');
    var btnTxt  = document.getElementById('cmt-btn-text');
    var errBox  = document.getElementById('cmt-error');

    errBox.style.display = 'none';

    if (!name)            { errBox.textContent = 'Veuillez indiquer votre nom.'; errBox.style.display = 'block'; return; }
    if (!content)         { errBox.textContent = 'Veuillez saisir un commentaire.'; errBox.style.display = 'block'; return; }
    if (content.length < 10) { errBox.textContent = 'Commentaire trop court (min. 10 caractères).'; errBox.style.display = 'block'; return; }
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errBox.textContent = "L'adresse e-mail saisie est invalide.";
        errBox.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btnTxt.textContent = 'Envoi en cours...';

    var fd = new FormData();
    fd.append('news_id',    DSM_NEWS_ID);
    fd.append('news_title', DSM_NEWS_TITLE);
    fd.append('user_name',  name);
    fd.append('user_email', email);
    fd.append('content',    content);

    fetch(DSM_API_BASE + '/api/comment.php', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        btn.disabled = false;
        btnTxt.textContent = 'Envoyer ma réaction';
        if (d.success) {
            document.getElementById('cmt-form-view').style.display    = 'none';
            document.getElementById('cmt-success-view').style.display = 'block';
            document.getElementById('cmt-content').value = '';
            document.getElementById('cmt-name').value    = '';
            document.getElementById('cmt-email').value   = '';
            var chars = document.getElementById('cmt-chars');
            if (chars) chars.textContent = '0';
        } else {
            errBox.textContent   = d.message;
            errBox.style.display = 'block';
        }
    })
    .catch(function(err) {
        console.error('Comment error:', err);
        btn.disabled = false;
        btnTxt.textContent   = 'Envoyer ma réaction';
        errBox.textContent   = 'Erreur réseau. Veuillez réessayer.';
        errBox.style.display = 'block';
    });
};

window.likeItem = function(id, type, btn) {
    var fd = new FormData();
    fd.append('id',   id);
    fd.append('type', type);

    fetch(DSM_API_BASE + '/api/like.php', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            if (type === 'news') {
                var counter = document.getElementById('news-likes-count');
                if (counter) counter.textContent = d.count;
                var svgEl = btn.querySelector('svg');
                if (svgEl) svgEl.style.fill = '#b91c1c';
                btn.style.color = '#b91c1c';
            } else {
                var countEl = btn.querySelector('.count');
                if (countEl) countEl.textContent = d.count;
                var svgEl2 = btn.querySelector('svg');
                if (svgEl2) svgEl2.style.fill = '#b91c1c';
            }
        }
    })
    .catch(function(err) { console.error('Like error:', err); });
};
</script>


<?php
function timeElapsedString($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $weeks = floor($diff->d / 7);
    $days = $diff->d - ($weeks * 7);
    $map = ['y'=>['an','ans'],'m'=>['mois','mois'],'w'=>['semaine','semaines'],'d'=>['jour','jours'],'h'=>['heure','heures'],'i'=>['minute','minutes'],'s'=>['seconde','secondes']];
    $vals = ['y'=>$diff->y,'m'=>$diff->m,'w'=>$weeks,'d'=>$days,'h'=>$diff->h,'i'=>$diff->i,'s'=>$diff->s];
    $parts = [];
    foreach ($map as $k => $labels) {
        if ($vals[$k]) { $parts[] = $vals[$k].' '.($vals[$k]>1?$labels[1]:$labels[0]); }
    }
    if (!$full) $parts = array_slice($parts, 0, 1);
    return $parts ? implode(', ', $parts) : 'un instant';
}
include 'includes/footer.php';
?>