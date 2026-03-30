<?php
require_once 'config/database.php';

$current_page = 'news';
$page_title = 'Actualités - Ir. Samy Magadju';
$page_description = 'Suivez les dernières actualités et actions de l\'Ir. Samy Magadju au service de la communauté';

// Pagination
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page_num - 1) * $per_page;

// Filtrage par catégorie
$category = isset($_GET['category']) ? $_GET['category'] : '';

$pdo = getDBConnection();

// Requête principale
$sql = "SELECT * FROM news WHERE published = 1";
if ($category) {
    $sql .= " AND category = :category";
}
$sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news_list = $stmt->fetchAll();

// Compter le total pour la pagination
$count_sql = "SELECT COUNT(*) FROM news WHERE published = 1";
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
$stmt = $pdo->query("SELECT DISTINCT category FROM news WHERE published = 1 AND category IS NOT NULL ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Actualité à la une (la plus récente)
$stmt = $pdo->query("SELECT * FROM news WHERE published = 1 ORDER BY created_at DESC LIMIT 1");
$featured_news = $stmt->fetch();

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-24 relative overflow-hidden transition-colors">
    <div class="absolute right-0 top-0 w-1/3 h-full bg-emerald-500/5 blur-[120px] rounded-full translate-x-1/2"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="space-y-4">
            <div class="w-12 h-1 bg-emerald-500 rounded-full"></div>
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 dark:text-white tracking-tight">Presse & <span class="text-emerald-600">Actualités</span></h1>
            <p class="text-xl text-slate-500 dark:text-slate-400 max-w-2xl font-light">Suivez en temps réel les actions, les plaidoyers et les engagements de l'Honorable Ir. Samy Magadju.</p>
        </div>
    </div>
</section>

<!-- Filtres Sticky -->
<section class="bg-white border-b border-slate-100 dark:border-slate-800 py-5 sticky top-0 z-40 backdrop-blur-md bg-white/90 dark:bg-slate-900/90 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégories :</span>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo SITE_URL; ?>/news.php"
                    class="px-5 py-1.5 rounded-full text-xs font-bold transition-all <?php echo !$category ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700'; ?>">
                    Toutes les publications
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo SITE_URL; ?>/news.php?category=<?php echo urlencode($cat); ?>"
                        class="px-5 py-1.5 rounded-full text-xs font-bold transition-all <?php echo $category === $cat ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Actualité à la Une -->
<?php if ($featured_news && !$category && $page_num === 1): ?>
    <section class="py-20 bg-white dark:bg-slate-900 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="bg-slate-900 rounded-[3rem] overflow-hidden flex flex-col lg:flex-row items-stretch shadow-2xl relative">
                <!-- Background Decorative Glow -->
                <div class="absolute top-0 left-0 w-64 h-64 bg-rose-500/10 blur-[80px] rounded-full"></div>

                <div class="w-full lg:w-1/2 p-10 md:p-16 flex flex-col justify-center relative z-10">
                    <div class="mb-8">
                        <span class="px-4 py-1.5 bg-rose-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg shadow-rose-500/20">
                            À la Une
                        </span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-black text-white mb-6 leading-tight group">
                        <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $featured_news['slug']; ?>" class="hover:text-rose-400 transition-colors">
                            <?php echo htmlspecialchars($featured_news['title']); ?>
                        </a>
                    </h2>

                    <p class="text-slate-400 text-base md:text-lg mb-8 leading-relaxed font-light">
                        <?php echo htmlspecialchars($featured_news['excerpt']); ?>
                    </p>

                    <div class="flex items-center gap-6 mb-10 pt-6 border-t border-white/5">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Publié le</span>
                            <span class="text-sm text-slate-300 font-semibold"><?php echo formatDateFR($featured_news['created_at']); ?></span>
                        </div>
                    </div>

                    <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $featured_news['slug']; ?>"
                        class="inline-flex items-center gap-3 text-rose-400 font-bold hover:text-rose-300 transition-all group">
                        Lire le reportage
                        <div class="w-10 h-10 rounded-full border border-rose-400/30 flex items-center justify-center group-hover:bg-rose-400 group-hover:text-slate-900 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </a>
                </div>

                <div class="w-full lg:w-1/2 relative bg-slate-800">
                    <?php if (!empty($featured_news['embed_code'])): ?>
                        <div class="w-full h-full flex items-center justify-center p-4">
                            <div class="w-full">
                                <?php echo parseEmbedCode($featured_news['embed_code']); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $featured_news['slug']; ?>" class="block h-full group">
                            <?php
                            $featured_image = $featured_news['image'] ? SITE_URL . '/uploads/' . $featured_news['image'] : null;
                            if (!$featured_image) {
                                $fallbacks = [
                                    'elu-2023-engagement-bukavu-bagira' => SITE_URL . '/uploads/profil.jpg',
                                    'expertise-energies-renouvelables' => SITE_URL . '/uploads/slng-dak.jpg',
                                    'actions-humanitaires-vulnerables' => SITE_URL . '/uploads/dk-jeunes.jpg'
                                ];
                                $featured_image = $fallbacks[$featured_news['slug']] ?? SITE_URL . '/uploads/dk-stade.jpg';
                            }
                            ?>
                            <img src="<?php echo $featured_image; ?>"
                                alt="<?php echo htmlspecialchars($featured_news['title']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-80 group-hover:opacity-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-100 transition-opacity"></div>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>
<?php endif; ?>

<!-- Liste des actualités -->
<section class="py-24 bg-slate-50 dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($news_list)): ?>
            <div class="text-center py-32 bg-white dark:bg-slate-800 rounded-[3rem] border border-dashed border-slate-200 dark:border-slate-700">
                <svg class="w-20 h-20 text-slate-200 dark:text-slate-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Pas de nouvelles publications</h3>
                <p class="text-slate-500 dark:text-slate-400">Revenez bientôt pour plus d'actualités.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <?php foreach ($news_list as $news): ?>
                    <article class="flex flex-col group">
                        <?php if (!empty($news['embed_code'])): ?>
                            <div class="w-full aspect-[16/10] rounded-[2rem] overflow-hidden mb-6 bg-slate-200 dark:bg-slate-800 shadow-sm flex items-center justify-center p-2">
                                <?php echo parseEmbedCode($news['embed_code']); ?>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $news['slug']; ?>"
                                class="block aspect-[16/10] rounded-[2rem] overflow-hidden mb-6 bg-slate-200 dark:bg-slate-800 shadow-sm group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-500">
                                <?php
                                $news_image = $news['image'] ? SITE_URL . '/uploads/' . $news['image'] : null;
                                if (!$news_image) {
                                    $fallbacks = [
                                        'elu-2023-engagement-bukavu-bagira' => SITE_URL . '/uploads/profil.jpg',
                                        'expertise-energies-renouvelables' => SITE_URL . '/uploads/slng-dak.jpg',
                                        'actions-humanitaires-vulnerables' => SITE_URL . '/uploads/dk-jeunes.jpg'
                                    ];
                                    $news_image = $fallbacks[$news['slug']] ?? SITE_URL . '/uploads/dk-stade.jpg';
                                }
                                ?>
                                <img src="<?php echo $news_image; ?>"
                                    alt="<?php echo htmlspecialchars($news['title']); ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </a>
                        <?php endif; ?>

                        <div class="space-y-4 px-2 flex-1 flex flex-col">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider rounded-lg">
                                    <?php echo htmlspecialchars($news['category'] ?: 'Info'); ?>
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    <?php echo formatDateFR($news['created_at']); ?>
                                </span>
                            </div>

                            <h3 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">
                                <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $news['slug']; ?>">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>

                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed line-clamp-3 font-light">
                                <?php echo htmlspecialchars($news['excerpt']); ?>
                            </p>

                            <div class="pt-4 mt-auto">
                                <a href="<?php echo SITE_URL; ?>/news-detail.php?slug=<?php echo $news['slug']; ?>" class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2 group/link">
                                    Lire la suite
                                    <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover/link:bg-emerald-600 group-hover/link:text-white transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination High End -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-24 pt-12 border-t border-slate-200 dark:border-slate-800">
                    <nav class="flex justify-center items-center gap-3">
                        <?php if ($page_num > 1): ?>
                            <a href="<?php echo SITE_URL; ?>/news.php?page=<?php echo $page_num - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
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
                                    <a href="<?php echo SITE_URL; ?>/news.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
                                        class="w-12 h-12 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold hover:border-slate-400 transition-all">
                                        <?php echo $i; ?>
                                    </a>
                                <?php elseif (abs($i - $page_num) === 2): ?>
                                    <span class="text-slate-300 px-1 font-bold">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page_num < $total_pages): ?>
                            <a href="<?php echo SITE_URL; ?>/news.php?page=<?php echo $page_num + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
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
    </div>
</section>
<?php include 'includes/footer.php'; ?>