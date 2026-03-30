<?php
require_once 'config/database.php';

// Récupérer le slug de l'actualité
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    header('Location: ' . SITE_URL . '/news.php');
    exit;
}

$pdo = getDBConnection();

// Récupérer l'actualité
$stmt = $pdo->prepare("SELECT * FROM news WHERE slug = :slug AND published = 1");
$stmt->execute([':slug' => $slug]);
$news = $stmt->fetch();

if (!$news) {
    header('Location: ' . SITE_URL . '/news.php');
    exit;
}

// Incrémenter le compteur de vues
$stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $news['id']]);

// 1. Articles de la même catégorie
$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id AND category = :category ORDER BY created_at DESC LIMIT 3");
$stmt->execute([':id' => $news['id'], ':category' => $news['category']]);
$category_news = $stmt->fetchAll();

// 2. Articles les plus récents
$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id ORDER BY created_at DESC LIMIT 3");
$stmt->execute([':id' => $news['id']]);
$recent_news = $stmt->fetchAll();

// 3. Articles les plus lus
$stmt = $pdo->prepare("SELECT * FROM news WHERE published = 1 AND id != :id ORDER BY views DESC LIMIT 3");
$stmt->execute([':id' => $news['id']]);
$popular_news = $stmt->fetchAll();

$current_page = 'news';
$page_title = htmlspecialchars($news['title']) . ' - Ir. Samy Magadju';
$page_description = htmlspecialchars($news['excerpt']);

include 'includes/header.php';
?>

<!-- Article -->
<article class="py-12 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8 text-sm">
            <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="hover:text-[rgb(var(--color-primary))]">Accueil</a></li>
                <li>›</li>
                <li><a href="<?php echo SITE_URL; ?>/news.php" class="hover:text-[rgb(var(--color-primary))]">Actualités</a></li>
                <?php if ($news['category']): ?>
                <li>›</li>
                <li><a href="<?php echo SITE_URL; ?>/news.php?category=<?php echo urlencode($news['category']); ?>" class="hover:text-[rgb(var(--color-primary))]"><?php echo htmlspecialchars($news['category']); ?></a></li>
                <?php endif; ?>
            </ol>
        </nav>

        <!-- Category & Date -->
        <div class="flex items-center gap-4 mb-6">
            <?php if ($news['category']): ?>
            <span class="text-sm font-bold text-[rgb(var(--color-primary))] uppercase tracking-wider">
                <?php echo htmlspecialchars($news['category']); ?>
            </span>
            <?php endif; ?>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                <?php echo formatDateFR($news['created_at']); ?>
            </span>
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
            <?php echo htmlspecialchars($news['title']); ?>
        </h1>

        <!-- Excerpt -->
        <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
            <?php echo htmlspecialchars($news['excerpt']); ?>
        </p>

        <!-- Meta info -->
        <div class="flex items-center gap-6 mb-8 text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span><?php echo htmlspecialchars($news['author']); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span><?php echo $news['views']; ?> lectures</span>
            </div>
        </div>

        <!-- Featured Image -->
        <?php 
        $article_image = $news['image'] ? SITE_URL . '/uploads/' . $news['image'] : null;
        if (!$article_image) {
            $fallbacks = [
                'elu-2023-engagement-bukavu-bagira' => SITE_URL . '/uploads/profil.jpg',
                'expertise-energies-renouvelables' => SITE_URL . '/uploads/slng-dak.jpg',
                'actions-humanitaires-vulnerables' => SITE_URL . '/uploads/dk-jeunes.jpg'
            ];
            $article_image = $fallbacks[$news['slug']] ?? SITE_URL . '/uploads/dk-stade.jpg';
        }
        ?>
        <figure class="mb-12">
            <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl">
                <img src="<?php echo $article_image; ?>" 
                     alt="<?php echo htmlspecialchars($news['title']); ?>"
                     class="w-full h-full object-cover">
            </div>
        </figure>

        <!-- Content -->
        <div class="prose prose-lg max-w-none">
            <?php echo $news['content']; ?>
        </div>

        <!-- Share Buttons -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Partager cet article</h3>
            <div class="flex gap-3 mb-12">
                <button onclick="shareOnFacebook()" class="px-6 py-3 bg-[#1877f2] text-white rounded-lg hover:bg-[#166fe5] transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </button>
                <button onclick="shareOnTwitter()" class="px-6 py-3 bg-[#1da1f2] text-white rounded-lg hover:bg-[#1a91da] transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    Twitter
                </button>
                <button onclick="shareOnWhatsApp()" class="px-6 py-3 bg-[#25d366] text-white rounded-lg hover:bg-[#20ba5a] transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    WhatsApp
                </button>
            </div>

            <!-- Facebook Style Comment System -->
            <?php
            // Gestion de l'ajout de commentaire
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
                $user_name = trim($_POST['user_name'] ?? 'Anonyme');
                $comment_content = trim($_POST['comment_content'] ?? '');
                
                // Anti-doublon simple : vérifier si le dernier commentaire de la session est identique
                $last_comment = $_SESSION['last_comment'] ?? '';
                $current_comment_hash = md5($news['id'] . $user_name . $comment_content);

                if ($comment_content && ($last_comment !== $current_comment_hash)) {
                    $stmt = $pdo->prepare("INSERT INTO comments (news_id, user_name, content, status) VALUES (:news_id, :user_name, :content, 'pending')");
                    $stmt->execute([
                        ':news_id' => $news['id'],
                        ':user_name' => $user_name,
                        ':content' => $comment_content
                    ]);
                    $_SESSION['last_comment'] = $current_comment_hash;
                    $_SESSION['last_comment_name'] = $user_name;
                    $comment_msg = "Commentaire envoyé pour modération !";
                }
            }

            // Récupérer les commentaires approuvés
            $stmt = $pdo->prepare("SELECT * FROM comments WHERE news_id = :id AND status = 'approved' ORDER BY created_at ASC");
            $stmt->execute([':id' => $news['id']]);
            $comments = $stmt->fetchAll();

            // Fonction pour générer un avatar avec initiale
            function getInitialAvatar($name) {
                $initial = strtoupper(substr($name, 0, 1));
                $colors = ['bg-emerald-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-rose-500', 'bg-indigo-500'];
                $color = $colors[ord($initial) % count($colors)];
                return '<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[11px] font-black '.$color.' shadow-sm transition-transform hover:scale-110">'.$initial.'</div>';
            }
            ?>

            <div class="max-w-2xl mx-auto mt-12 mb-20 bg-white dark:bg-slate-900 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-slate-800">
                
                <!-- Like Article -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-50 dark:border-slate-800">
                    <button onclick="handleLike(<?php echo $news['id']; ?>, 'news', this)" 
                            class="flex items-center gap-2 group text-gray-500 hover:text-emerald-600 transition-all font-bold text-sm">
                        <svg class="w-5 h-5 group-active:scale-150 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h2a2 2 0 012 2v1a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h2.251M17 10a2 2 0 012 2v5a2 2 0 01-2 2h-7a2 2 0 01-2-2v-5a2 2 0 012-2m3.93 1a.996.996 0 000 1h1a1 1 0 011 1v1a1 1 0 01-1 1h-1a1 1 0 01-1-1V11a1 1 0 011-1h1z" /></svg>
                        <span>J'aime cet article</span>
                        <span class="bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded-full text-[10px] count-display"><?php echo $news['likes'] ?? 0; ?></span>
                    </button>
                    <h3 class="text-gray-900 dark:text-white font-bold text-lg flex items-center gap-2">
                        Commentaires
                        <span class="text-xs font-normal text-gray-400"><?php echo count($comments); ?></span>
                    </h3>
                </div>

                <?php if(isset($comment_msg)): ?>
                    <div class="mb-6 p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-bold border border-emerald-100 dark:border-emerald-900/30 animate-fade-in">
                        <?php echo $comment_msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Liste des commentaires -->
                <div id="comment-section" class="space-y-5">
                    <?php if (empty($comments)): ?>
                        <p class="text-gray-400 dark:text-slate-500 text-sm italic text-center py-4">Aucun commentaire pour le moment.</p>
                    <?php else: ?>
                        <?php foreach($comments as $comment): ?>
                            <div class="flex gap-3 group animate-fade-in">
                                <?php echo getInitialAvatar($comment['user_name']); ?>
                                <div class="flex flex-col max-w-[85%]">
                                    <div class="bg-gray-100 dark:bg-slate-800 rounded-2xl px-4 py-2.5">
                                        <p class="text-[13px] font-bold text-gray-900 dark:text-white leading-tight mb-0.5"><?php echo htmlspecialchars($comment['user_name']); ?></p>
                                        <p class="text-[14px] text-gray-700 dark:text-gray-300 leading-snug"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                    </div>
                                    <div class="flex items-center gap-4 ml-2 mt-1 text-[11px] font-bold text-gray-500 dark:text-slate-400">
                                        <button onclick="handleLike(<?php echo $comment['id']; ?>, 'comment', this)" class="hover:underline flex items-center gap-1">
                                            <span>J'aime</span>
                                            <span class="font-normal count-display">(<?php echo $comment['likes'] ?? 0; ?>)</span>
                                        </button>
                                        <button class="hover:underline">Répondre</button>
                                        <span class="font-normal text-gray-400 dark:text-slate-500"><?php echo date('d M', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <hr class="my-6 border-gray-100 dark:border-slate-800">

                <!-- Zone de saisie -->
                <form method="POST" class="space-y-3" id="facebook-comment-form">
                    <div class="flex gap-2 mb-2 items-center">
                        <div class="w-8"></div>
                        <input type="text" name="user_name" placeholder="Votre nom ou e-mail..." required
                               class="bg-transparent border-none p-0 text-[12px] font-bold text-emerald-600 focus:ring-0 placeholder-gray-400 w-full"
                               value="<?php echo htmlspecialchars($_SESSION['last_comment_name'] ?? ''); ?>">
                    </div>

                    <div class="flex gap-2 items-start">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400">?</div>
                        <div class="flex-1 bg-gray-100 dark:bg-slate-800 rounded-2xl px-4 py-2 flex items-center transition-all focus-within:ring-2 focus-within:ring-emerald-500/20">
                            <textarea 
                                name="comment_content" 
                                id="comment-textarea"
                                rows="1"
                                placeholder="Appuyez sur Entrée pour commenter..." 
                                class="bg-transparent w-full focus:outline-none text-[14px] text-gray-800 dark:text-white placeholder-gray-400 py-1 resize-none overflow-hidden"
                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                onkeypress="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); if(this.value.trim()) this.form.submit(); }"
                            ></textarea>
                            <input type="hidden" name="submit_comment" value="1">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</article>

<script>
function handleLike(id, type, btn) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('type', type);

    fetch('api/like.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const countDisplay = btn.querySelector('.count-display');
            if (countDisplay) {
                if (type === 'comment') {
                    countDisplay.textContent = `(${data.count})`;
                } else {
                    countDisplay.textContent = data.count;
                }
            }
            btn.classList.add('text-emerald-600');
            btn.classList.add('scale-110');
            setTimeout(() => btn.classList.remove('scale-110'), 200);
        }
    });
}
</script>
            </div>
        </div>
    </div>
</article>

<!-- Related News -->
<!-- Recommended Content Section -->
<section class="py-16 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-12 text-center">Découvrez plus d'actualités</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- 1. Même catégorie -->
            <?php if (!empty($category_news)): ?>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-[rgb(var(--color-primary))] rounded-full"></span>
                    Dans la même catégorie
                </h3>
                <div class="space-y-6">
                    <?php foreach ($category_news as $item): ?>
                    <article class="flex gap-4 group">
                        <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $item['slug']; ?>" class="shrink-0 w-24 h-24 rounded-lg overflow-hidden bg-gray-200">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo SITE_URL . '/uploads/' . $item['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div>
                            <span class="text-xs font-semibold text-[rgb(var(--color-primary))] mb-1 block">
                                <?php echo formatDateFR($item['created_at']); ?>
                            </span>
                            <h4 class="font-bold text-gray-900 dark:text-white leading-tight group-hover:text-[rgb(var(--color-primary))] transition-colors line-clamp-2">
                                <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $item['slug']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h4>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 2. Plus récents -->
            <?php if (!empty($recent_news)): ?>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                    Les plus récents
                </h3>
                <div class="space-y-6">
                    <?php foreach ($recent_news as $item): ?>
                    <article class="flex gap-4 group">
                        <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $item['slug']; ?>" class="shrink-0 w-24 h-24 rounded-lg overflow-hidden bg-gray-200">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo SITE_URL . '/uploads/' . $item['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div>
                            <span class="text-xs font-semibold text-blue-500 mb-1 block">
                                <?php echo formatDateFR($item['created_at']); ?>
                            </span>
                            <h4 class="font-bold text-gray-900 dark:text-white leading-tight group-hover:text-blue-500 transition-colors line-clamp-2">
                                <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $item['slug']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h4>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 3. Plus lus -->
            <?php if (!empty($popular_news)): ?>
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-red-500 rounded-full"></span>
                    Les plus lus
                </h3>
                <div class="space-y-6">
                    <?php foreach ($popular_news as $index => $item): ?>
                    <article class="flex gap-4 group items-center">
                        <div class="shrink-0 w-8 text-2xl font-black text-gray-200 dark:text-slate-800">
                            <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span><?php echo $item['views']; ?> lectures</span>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white leading-tight group-hover:text-red-500 transition-colors line-clamp-2">
                                <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $item['slug']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h4>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<script>
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(document.querySelector('h1').textContent);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(document.querySelector('h1').textContent);
    window.open(`https://wa.me/?text=${text} ${url}`, '_blank');
}
</script>



<?php include 'includes/footer.php'; ?>
