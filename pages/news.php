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
$sql = "SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1";
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
$stmt = $pdo->query("SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1 ORDER BY n.created_at DESC LIMIT 1");
$featured_news = $stmt->fetch();

include 'includes/header.php';
?>

<!-- Page Header Figaro Style -->
<section class="bg-white dark:bg-slate-900 border-b-[3px] border-black dark:border-white py-12 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-5xl md:text-7xl font-black text-black dark:text-white uppercase tracking-widest font-serif mb-4">L'Actualité</h1>
            <p class="text-xl text-gray-500 font-serif">Retrouvez tous les articles, parutions et médias concernant Ir. Samy Magadju.</p>
        </div>
    </div>
</section>

<!-- Filtres Sticky Figaro Style -->
<section class="bg-white dark:bg-slate-900 border-b border-gray-300 dark:border-gray-800 py-4 sticky top-0 z-40 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex flex-wrap justify-center gap-6">
            <a href="<?php echo $router->generate('news_list'); ?>"
                class="text-sm font-bold uppercase tracking-widest transition-colors <?php echo !$category ? 'text-red-700 dark:text-red-500 border-b-2 border-red-700 dark:border-red-500' : 'text-gray-600 dark:text-gray-400 hover:text-red-700 dark:hover:text-red-500'; ?>">
                La Une
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo $router->generate('news_list'); ?>?category=<?php echo urlencode($cat); ?>"
                    class="text-sm font-bold uppercase tracking-widest transition-colors <?php echo $category === $cat ? 'text-red-700 dark:text-red-500 border-b-2 border-red-700 dark:border-red-500' : 'text-gray-600 dark:text-gray-400 hover:text-red-700 dark:hover:text-red-500'; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
                        <a href="<?php echo getUrl('news', $featured_news['id'], $featured_news['slug']); ?>" class="block">
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
                        <span class="text-sm text-gray-400 font-serif"><?php echo formatDateFR($featured_news['created_at']); ?></span>
                        <span class="text-gray-300 dark:text-gray-700 mx-2">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest"><?php echo htmlspecialchars($featured_news['author_name'] ?: 'La Rédaction'); ?></span>
                        <span class="text-gray-300 dark:text-gray-700 mx-2">•</span>
                        <span class="text-sm text-gray-400 font-serif"><?php echo getReadingTime($featured_news['content']); ?> min</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-bold font-serif leading-tight text-black dark:text-white mb-6 group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors">
                        <a href="<?php echo getUrl('news', $featured_news['id'], $featured_news['slug']); ?>">
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
                            <a href="<?php echo getUrl('news', $news['id'], $news['slug']); ?>"
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
                                    <?php echo formatDateFR($news['created_at']); ?>
                                </span>
                                <span class="text-gray-300 dark:text-gray-700">•</span>
                                <span class="text-[9px] font-black text-gray-900 dark:text-gray-300 uppercase"><?php echo htmlspecialchars($news['author_name'] ?: 'Rédaction'); ?></span>
                                <span class="text-gray-300 dark:text-gray-700">•</span>
                                <span class="text-[10px] text-gray-500 font-serif">
                                    <?php echo getReadingTime($news['content']); ?> min
                                </span>
                            </div>

                            <h3 class="text-2xl font-bold font-serif leading-tight group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors text-black dark:text-white mb-3">
                                <a href="<?php echo getUrl('news', $news['id'], $news['slug']); ?>">
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
    </div>
</section>
<?php include 'includes/footer.php'; ?>
