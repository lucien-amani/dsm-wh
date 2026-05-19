<?php
require_once 'config/database.php';

$current_page = 'home';
$page_title = 'Accueil - Dynamique Samy Magadju/Wema ni Hakiba';
$page_description = 'Bienvenue sur le site officiel de la Dynamique Samy Magadju/Wema ni Hakiba. Ensemble pour le développement intégral, l\'innovation et le bien-être social au Sud-Kivu.';

// Récupérer les dernières actualités (100 premières)
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT n.*, u.full_name as author_name, u.avatar as author_avatar FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1 AND n.deleted_at IS NULL ORDER BY n.created_at DESC LIMIT 100");
$latest_news = $stmt->fetchAll();

// Récupérer les articles les plus vus
$stmt = $pdo->query("SELECT n.*, u.full_name as author_name, u.avatar as author_avatar FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.published = 1 AND n.deleted_at IS NULL ORDER BY n.views DESC LIMIT 6");
$popular_news = $stmt->fetchAll();

// Récupérer les projets récents
$stmt = $pdo->query("SELECT * FROM projects WHERE published = 1 AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 3");
$latest_projects = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-gray-900 to-gray-800 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 animate-fade-in">
                <div class="inline-block px-4 py-2 bg-[rgb(var(--color-primary))]/20 rounded-full text-sm font-semibold">
                    Honorable & Ingénieur
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                    Ir. Samy Magadju
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 font-light">
                    Ensemble pour le développement intégral
                </p>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Leader dévoué au service de sa communauté, expert en énergies renouvelables et 
                    défenseur de la bonne gouvernance. Élu en 2023 pour servir Bukavu et la commune de Bagira.
                </p>
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="<?php echo $router->generate('about'); ?>" class="btn-primary">
                        Découvrir mon parcours
                    </a>
                    <a href="<?php echo $router->generate('contact'); ?>" class="btn-secondary">
                        Nous contacter
                    </a>
                </div>
            </div>
            <div class="relative flex justify-center lg:justify-end animate-fade-in" style="animation-delay: 200ms;">
                <div class="relative group">
                    <!-- Décoration arrière -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-[rgb(var(--color-primary))] to-red-500 rounded-[2rem] blur-2xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                    
                    <!-- Conteneur Image (Square to match About page) -->
                    <div class="relative w-full max-w-[500px] aspect-square rounded-2xl overflow-hidden border-4 border-white/10 shadow-2xl">
                        <img src="<?php echo SITE_URL; ?>/uploads/profil.jpg" 
                             alt="Ir. Samy Magadju" 
                             class="w-full h-full object-cover transition-all duration-700 scale-105 group-hover:scale-100">
                        
                        <!-- Overlay dégradé -->
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent"></div>
                    </div>
                    
                    <!-- Badge flottant -->
                    <div class="absolute -bottom-6 -left-6 bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-xl border border-gray-100 dark:border-white/5 hidden md:block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[rgb(var(--color-primary))] rounded-full flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Élu en 2023</p>
                                <p class="text-sm font-black text-gray-900 dark:text-white">Conseiller Municipal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!--  -->
<!-- =========================================================
     SECTION ACTUALITÉS - STYLE LE FIGARO (SYNCHRONISÉ)
     ========================================================= -->
<?php if (!empty($latest_news)): 
    $news_list = array_slice($latest_news, 0, 6); // On prend les 6 dernières
    $featured_news = array_shift($news_list); // La plus récente pour la Une
?>
<!-- En-tête de section -->
<section class="bg-white dark:bg-slate-900 border-t-4 border-black dark:border-white pt-16 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-baseline border-b-2 border-black dark:border-white pb-4 mb-12">
            <h2 class="text-4xl md:text-6xl font-black text-black dark:text-white uppercase tracking-tighter font-serif">Actualités</h2>
            <a href="<?php echo SITE_URL; ?>/actualites" class="text-red-700 dark:text-red-500 font-bold uppercase tracking-widest text-sm hover:underline mt-4 md:mt-0">
                Toutes les actualités →
            </a>
        </div>
    </div>
</section>

<!-- Actualité à la Une -->
<?php if ($featured_news): ?>
    <section class="py-12 bg-white dark:bg-slate-900 transition-colors border-b border-gray-300 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="flex flex-col lg:flex-row gap-8 items-start relative group">
                <div class="w-full lg:w-2/3 border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-slate-800 overflow-hidden">
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
                        <span class="text-sm text-gray-400 font-serif"><?php echo formatDateFR($featured_news['created_at']); ?></span>
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

<!-- Grille d'articles secondaires -->
<section class="py-12 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    </div>
</section>
<?php endif; ?>


<!-- Projets récents -->
<?php if (!empty($latest_projects)): ?>
<section class="py-20 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="section-title inline-block">Projets Récents</h2>
                <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">Des réalisations concrètes au service du développement</p>
            </div>
            <a href="<?php echo $router->generate('projects_list'); ?>" class="hidden md:inline-block text-[rgb(var(--color-primary))] font-semibold hover:underline">
                Voir tous les projets →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($latest_projects as $project): ?>
            <article class="card group">
                <?php if (!empty($project['embed_code'])): ?>
                    <div class="aspect-video bg-gray-200 overflow-hidden relative flex items-center justify-center p-2">
                        <?php echo parseEmbedCode($project['embed_code']); ?>
                    </div>
                <?php else: ?>
                    <div class="aspect-video bg-gray-200 overflow-hidden relative">
                        <?php 
                        $project_image = $project['image'] ? SITE_URL . '/uploads/' . $project['image'] : null;
                        if (!$project_image) {
                            // Fallback images from uploads folder
                            $fallbacks = [
                                'centrale-hydroelectrique-lualaba' => SITE_URL . '/uploads/akd-assemblee.jpg',
                                'forages-eau-potable-sud-kivu' => SITE_URL . '/uploads/alongo-dka.jpg',
                                'installation-solaire-ecoles-rurales' => SITE_URL . '/uploads/dk-kilio.jpg'
                            ];
                            $project_image = $fallbacks[$project['slug']] ?? SITE_URL . '/uploads/quartier-dk.jpg';
                        }
                        ?>
                        <img src="<?php echo $project_image; ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                <?php endif; ?>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-semibold text-white bg-[rgb(var(--color-primary))] px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($project['status']); ?>
                        </span>
                        <?php if ($project['location']): ?>
                        <span class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <?php echo htmlspecialchars($project['location']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 group-hover:text-[rgb(var(--color-primary))] transition-colors">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>
                    <a href="<?php echo getUrl('projects', $project['id'], $project['slug'], $project['nanoid'] ?? null); ?>" 
                       class="inline-flex items-center text-[rgb(var(--color-primary))] font-semibold hover:gap-2 transition-all">
                        Voir le projet
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="md:hidden text-center mt-8">
            <a href="<?php echo $router->generate('projects_list'); ?>" class="text-[rgb(var(--color-primary))] font-semibold hover:underline">
                Voir tous les projets →
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section class="py-20 bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Une Question ? Un Projet ?</h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            N'hésitez pas à nous contacter pour toute question ou pour discuter de vos projets. 
            Nous sommes à votre écoute.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="<?php echo $router->generate('contact'); ?>" class="px-8 py-4 bg-white text-[rgb(var(--color-primary))] font-semibold rounded-lg hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl">
                Nous contacter
            </a>
            <a href="tel:+243993859330" class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-[rgb(var(--color-primary))] transition-colors">
                +243 993 859 330
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
