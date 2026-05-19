<?php
// pages/news_list.php
// Ce fichier remplace l'ancien news.php à la racine

$current_page = 'news';
$page_title = 'Actualités - Dynamique Samy Magadju/Wema ni Hakiba';
$page_description = 'Suivez les dernières actualités, activités et interventions de la Dynamique Samy Magadju/Wema ni Hakiba au Sud-Kivu.';

// Pagination
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page_num - 1) * $per_page;

// Filtrage par catégorie
$category = isset($_GET['category']) ? $_GET['category'] : '';

$pdo = getDBConnection();

// Requête principale
$sql = "SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1 AND n.deleted_at IS NULL";
if ($category) {
    $sql .= " AND n.category = :category";
}
$sql .= " ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news_list = $stmt->fetchAll();

// Compter le total pour la pagination
$count_sql = "SELECT COUNT(*) FROM news WHERE published = 1 AND deleted_at IS NULL";
if ($category) {
    $count_sql .= " AND category = :category";
}
$stmt = $pdo->prepare($count_sql);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->execute();
$total_news = $stmt->fetchColumn();
$total_pages = ceil($total_news / $per_page);

// Récupérer les catégories
$stmt = $pdo->query("SELECT DISTINCT category FROM news WHERE published = 1 AND deleted_at IS NULL AND category IS NOT NULL ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Actualité à la une (la plus récente)
$stmt = $pdo->query("SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1 AND n.deleted_at IS NULL ORDER BY n.created_at DESC LIMIT 1");
$featured_news = $stmt->fetch();

include 'includes/header.php';
?>

<!-- Filtres : Version Mobile (Tout en haut, remplace le header) -->
<section id="news-filter-bar-mobile" class="lg:hidden bg-white dark:bg-slate-900 border-b border-gray-300 dark:border-gray-800 py-3 sticky top-0 z-[60] shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex overflow-x-auto no-scrollbar whitespace-nowrap items-center gap-6 py-1">
            <a href="<?php echo $router->generate('news_list'); ?>" class="text-sm font-bold uppercase tracking-widest flex-shrink-0 <?php echo !$category ? 'text-red-700 dark:text-red-500 border-b-2 border-red-700 dark:border-red-500' : 'text-gray-600 dark:text-gray-400'; ?>">La Une</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo $router->generate('news_list'); ?>?category=<?php echo urlencode($cat); ?>" class="text-sm font-bold uppercase tracking-widest flex-shrink-0 <?php echo $category === $cat ? 'text-red-700 dark:text-red-500 border-b-2 border-red-700 dark:border-red-500' : 'text-gray-600 dark:text-gray-400'; ?>"><?php echo htmlspecialchars($cat); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Page Header Figaro Style (Bureau & Mobile) -->
<section class="bg-white dark:bg-slate-900 border-b-[3px] border-black dark:border-white py-12 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-5xl md:text-7xl font-black text-black dark:text-white uppercase tracking-widest font-serif mb-4">L'Actualité</h1>
            <p class="text-xl text-gray-500 font-serif">Retrouvez tous les articles, parutions et médias concernant Ir. Samy Magadju.</p>
        </div>
    </div>
</section>

<!-- Filtres : Version Bureau (Position classique sous le titre) -->
<section class="hidden lg:block bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-gray-800 py-6 sticky top-[80px] z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Parcourir :</span>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo $router->generate('news_list'); ?>" 
                       class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all <?php echo !$category ? 'bg-red-700 text-white shadow-lg shadow-red-700/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-750'; ?>">
                        La Une
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo $router->generate('news_list'); ?>?category=<?php echo urlencode($cat); ?>" 
                           class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all <?php echo $category === $cat ? 'bg-red-700 text-white shadow-lg shadow-red-700/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-750'; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                <?php echo $total_news; ?> Article(s)
            </div>
        </div>
    </div>
</section>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @media (max-width: 1023px) {
        nav.sticky { display: none !important; }
        body { padding-top: 0 !important; }
    }
</style>

<script>
// Le script de scroll n'est plus nécessaire si on le masque par défaut via CSS, 
// mais on peut le garder pour gérer le comportement desktop si besoin.
</script>

<!-- Actualité à la Une -->
<?php if ($featured_news && !$category && $page_num === 1): ?>
    <section class="py-12 bg-white dark:bg-slate-900 transition-colors border-b border-gray-300 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="flex flex-col lg:flex-row gap-8 items-start relative group">
                <div class="w-full lg:w-2/3 border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-slate-800">
                    <?php if (!empty($featured_news['embed_code'])): ?>
                        <div class="w-full flex justify-center p-1">
                            <?php echo parseEmbedCode($featured_news['embed_code']); ?>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo getUrl('news', $featured_news['id'], $featured_news['slug'], $featured_news['nanoid'] ?? null); ?>" class="block">
                            <?php
                            $featured_image = $featured_news['image'] ? SITE_URL . '/uploads/' . $featured_news['image'] : SITE_URL . '/uploads/profil.jpg';
                            ?>
                            <img src="<?php echo $featured_image; ?>"
                                alt="<?php echo htmlspecialchars($featured_news['title']); ?>"
                                class="w-full max-h-[600px] object-cover hover:scale-105 transition-transform duration-700">
                        </a>
                    <?php endif; ?>
                </div>

                <div class="w-full lg:w-1/3 flex flex-col justify-center lg:py-4">
                    <div class="mb-4">
                        <span class="text-sm font-bold text-red-700 dark:text-red-500 uppercase tracking-widest">À LA UNE</span>
                        <span class="text-gray-300 dark:text-gray-700 mx-2">|</span>
                        <span class="text-sm text-gray-400 font-serif" title="<?php echo date('d/m/Y à H:i', strtotime($featured_news['created_at'])); ?>"><?php echo formatSmartDate($featured_news['created_at']); ?></span>
                        <span class="text-gray-300 dark:text-gray-700 mx-2">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest"><?php echo htmlspecialchars($featured_news['author_name'] ?: 'La Rédaction'); ?></span>
                        <span class="text-gray-300 dark:text-gray-700 mx-2">•</span>
                        <span class="text-sm text-gray-400 font-serif"><?php echo getReadingTime($featured_news['content']); ?> min</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-bold font-serif leading-tight text-black dark:text-white mb-6 group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors">
                        <a href="<?php echo getUrl('news', $featured_news['id'], $featured_news['slug'], $featured_news['nanoid'] ?? null); ?>">
                            <?php echo htmlspecialchars($featured_news['title']); ?>
                        </a>
                    </h2>

                    <p class="text-xl text-gray-700 dark:text-gray-300 font-serif leading-relaxed line-clamp-4">
                        <?php echo htmlspecialchars($featured_news['excerpt']); ?>
                    </p>
                </div>
            </article>
        </div>
    </section>
<?php endif; ?>

<!-- SLOT A: NEWS LIST TOP -->
<?php $ads_a = getActiveAds('news_list_top'); ?>
<?php if (!empty($ads_a)): $ad = $ads_a[0]; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
    <div class="ad-unit group relative bg-slate-50 dark:bg-slate-800/50 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800" data-ad-id="<?php echo $ad['id']; ?>">
        <span class="absolute top-2 right-4 text-[8px] font-black uppercase tracking-[0.3em] text-slate-400 z-10">Sponsorisé</span>
        <a href="<?php echo e($ad['link']); ?>" target="_blank" onclick="trackAd(<?php echo $ad['id']; ?>, 'click')" class="block">
            <img src="<?php echo SITE_URL; ?>/uploads/ads/<?php echo $ad['image']; ?>" alt="Publicité" class="w-full h-auto max-h-[150px] object-cover">
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Liste des actualités (Le Figaro Grid) -->
<section class="py-12 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($news_list)): ?>
            <div class="text-center py-24 border-y border-gray-300 dark:border-gray-800">
                <h3 class="text-3xl font-serif text-black dark:text-white mb-4">Aucun article publié pour le moment.</h3>
                <p class="text-gray-500 font-serif">Veuillez revenir consulter cette section plus tard.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
                <?php foreach ($news_list as $news): ?>
                    <article class="group border-b border-gray-300 dark:border-gray-800 pb-8 flex flex-col">
                        <?php if (!empty($news['embed_code'])): ?>
                            <div class="w-full mb-4 bg-gray-50 dark:bg-slate-800 border overflow-hidden max-h-[350px]">
                                <div class="w-full h-full p-2 origin-top flex justify-center">
                                    <?php echo parseEmbedCode($news['embed_code']); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo getUrl('news', $news['id'], $news['slug'], $news['nanoid'] ?? null); ?>"
                                class="block w-full mb-4 overflow-hidden border border-gray-200 dark:border-gray-800">
                                <?php
                                $news_image = $news['image'] ? SITE_URL . '/uploads/' . $news['image'] : SITE_URL . '/uploads/profil.jpg';
                                ?>
                                <img src="<?php echo $news_image; ?>"
                                    alt="<?php echo htmlspecialchars($news['title']); ?>"
                                    class="w-full aspect-[4/3] object-cover hover:scale-105 transition-transform duration-700">
                            </a>
                        <?php endif; ?>

                        <div class="flex-1 flex flex-col">
                            <div class="flex flex-wrap items-center gap-2 mb-2 mt-2">
                                <span class="text-[10px] font-bold text-red-700 dark:text-red-500 uppercase tracking-widest">
                                    <?php echo htmlspecialchars($news['category'] ?: 'INFO'); ?>
                                </span>
                                <span class="text-[10px] text-gray-500 font-serif">
                                    <span title="<?php echo date('d/m/Y à H:i', strtotime($news['created_at'])); ?>"><?php echo formatSmartDate($news['created_at']); ?></span>
                                </span>
                                <span class="text-gray-300 dark:text-gray-700">•</span>
                                <span class="text-[9px] font-black text-gray-900 dark:text-gray-300 uppercase"><?php echo htmlspecialchars($news['author_name'] ?: 'Rédaction'); ?></span>
                                <span class="text-gray-300 dark:text-gray-700">•</span>
                                <span class="text-[10px] text-gray-500 font-serif">
                                    <?php echo getReadingTime($news['content']); ?> min
                                </span>
                            </div>

                            <h3 class="text-2xl font-bold font-serif leading-tight group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors text-black dark:text-white mb-3">
                                <a href="<?php echo getUrl('news', $news['id'], $news['slug'], $news['nanoid'] ?? null); ?>">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>

                            <p class="text-base text-gray-600 dark:text-gray-400 font-serif leading-relaxed line-clamp-3">
                                <?php echo htmlspecialchars($news['excerpt']); ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- SLOT B: NEWS LIST INFEED (After grid) -->
            <?php $ads_b = getActiveAds('news_list_infeed'); ?>
            <?php if (!empty($ads_b)): $ad = $ads_b[0]; ?>
            <div class="mt-16">
                <div class="ad-unit group relative bg-white dark:bg-slate-900 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2.5rem] p-4" data-ad-id="<?php echo $ad['id']; ?>">
                    <span class="absolute -top-3 left-8 px-4 bg-white dark:bg-slate-900 text-[9px] font-black uppercase tracking-widest text-amber-600 border border-amber-100 dark:border-amber-900/30 rounded-full">Partenaire</span>
                    <a href="<?php echo e($ad['link']); ?>" target="_blank" onclick="trackAd(<?php echo $ad['id']; ?>, 'click')" class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="w-full md:w-1/3 aspect-video rounded-3xl overflow-hidden shadow-xl">
                            <img src="<?php echo SITE_URL; ?>/uploads/ads/<?php echo $ad['image']; ?>" alt="Ad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        </div>
                        <div class="flex-1 text-center md:text-left space-y-3">
                            <h4 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter"><?php echo e($ad['title']); ?></h4>
                            <p class="text-slate-500 font-medium">Découvrez les solutions innovantes de nos partenaires pour le développement du Sud-Kivu.</p>
                            <div class="inline-flex items-center gap-2 text-amber-600 font-black text-[10px] uppercase tracking-widest border-b-2 border-amber-600/20 pb-1">En savoir plus →</div>
                        </div>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Pagination High End -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-24 pt-12 border-t border-slate-200 dark:border-slate-800">
                    <nav class="flex justify-center items-center gap-3">
                        <?php if ($page_num > 1): ?>
                            <a href="<?php echo $router->generate('news_list'); ?>?page=<?php echo $page_num - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
                                class="w-12 h-12 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-900 dark:text-white hover:bg-slate-900 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        <?php endif; ?>

                        <div class="flex items-center gap-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i === $page_num): ?>
                                    <span class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-lg shadow-emerald-600/20">
                                        <?php echo $i; ?>
                                    </span>
                                <?php elseif ($i === 1 || $i === $total_pages || abs($i - $page_num) <= 1): ?>
                                    <a href="<?php echo $router->generate('news_list'); ?>?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
                                        class="w-12 h-12 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold hover:border-slate-400 transition-all">
                                        <?php echo $i; ?>
                                    </a>
                                <?php elseif (abs($i - $page_num) === 2): ?>
                                    <span class="text-slate-300 px-1 font-bold">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page_num < $total_pages): ?>
                            <a href="<?php echo $router->generate('news_list'); ?>?page=<?php echo $page_num + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
                                class="w-12 h-12 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-900 dark:text-white hover:bg-slate-900 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- SLOT C: NEWS LIST BOTTOM -->
        <?php $ads_c = getActiveAds('news_list_bottom'); ?>
        <?php if (!empty($ads_c)): $ad = $ads_c[0]; ?>
        <div class="mt-24 pt-12 border-t border-slate-100 dark:border-slate-800">
            <div class="ad-unit max-w-4xl mx-auto rounded-2xl overflow-hidden shadow-sm" data-ad-id="<?php echo $ad['id']; ?>">
                <a href="<?php echo e($ad['link']); ?>" target="_blank" onclick="trackAd(<?php echo $ad['id']; ?>, 'click')" class="block">
                    <img src="<?php echo SITE_URL; ?>/uploads/ads/<?php echo $ad['image']; ?>" alt="Ad" class="w-full h-auto">
                </a>
                <div class="bg-slate-50 dark:bg-slate-800/50 py-2 px-4 text-center">
                    <span class="text-[8px] font-black uppercase tracking-[0.4em] text-slate-400">- Espace Publicitaire -</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
