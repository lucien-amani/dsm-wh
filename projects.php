<?php
require_once 'config/database.php';

$current_page = 'projects';
$page_title = 'Projets - Ir. Samy Magadju';
$page_description = 'Découvrez les projets réalisés et en cours par l\'Ir. Samy Magadju dans les domaines de l\'énergie, de l\'eau et du développement';

// Filtrage par catégorie et statut
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$pdo = getDBConnection();

// Requête principale
$sql = "SELECT * FROM projects WHERE published = 1";
$params = [];

if ($category) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

if ($status) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Récupérer les statistiques
$stmt = $pdo->query("SELECT COUNT(*) as total, status FROM projects WHERE published = 1 GROUP BY status");
$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Nos Projets</h1>
            <p class="text-xl text-white/90 max-w-3xl mx-auto">
                Des réalisations concrètes au service du développement durable et du bien-être de nos communautés
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2"><?php echo array_sum($stats); ?></div>
                <div class="text-white/90">Projets Totaux</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2"><?php echo $stats['En cours'] ?? 0; ?></div>
                <div class="text-white/90">Projets En Cours</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2"><?php echo $stats['Terminé'] ?? 0; ?></div>
                <div class="text-white/90">Projets Terminés</div>
            </div>
        </div>
    </div>
</section>

<!-- Filtres -->
<section class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-gray-700 py-6 sticky top-20 z-40 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Statut:</label>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo SITE_URL; ?>/projects.php<?php echo $category ? '?category=' . urlencode($category) : ''; ?>" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo !$status ? 'bg-[rgb(var(--color-primary))] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-slate-800 dark:text-gray-300 dark:hover:bg-slate-700'; ?>">
                        Tous
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projects.php?status=En cours<?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo $status === 'En cours' ? 'bg-[rgb(var(--color-primary))] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-slate-800 dark:text-gray-300 dark:hover:bg-slate-700'; ?>">
                        En cours
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projects.php?status=Terminé<?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo $status === 'Terminé' ? 'bg-[rgb(var(--color-primary))] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-slate-800 dark:text-gray-300 dark:hover:bg-slate-700'; ?>">
                        Terminé
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projects.php?status=Planifié<?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo $status === 'Planifié' ? 'bg-[rgb(var(--color-primary))] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-slate-800 dark:text-gray-300 dark:hover:bg-slate-700'; ?>">
                        Planifié
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Liste des projets -->
<section class="py-16 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($projects)): ?>
        <div class="text-center py-20">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Aucun projet trouvé</h3>
            <p class="text-gray-600 dark:text-gray-400">Revenez bientôt pour découvrir nos nouveaux projets</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($projects as $project): ?>
            <article class="card group">
                <div class="aspect-video bg-gray-200 overflow-hidden relative">
                    <?php 
                    $project_image = $project['image'] ? SITE_URL . '/uploads/' . $project['image'] : null;
                    if (!$project_image) {
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
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <?php
                        $statusColors = [
                            'En cours' => 'bg-blue-500',
                            'Terminé' => 'bg-green-500',
                            'Planifié' => 'bg-orange-500'
                        ];
                        $statusColor = $statusColors[$project['status']] ?? 'bg-gray-500';
                        ?>
                        <span class="<?php echo $statusColor; ?> text-white text-xs font-bold px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($project['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if ($project['category']): ?>
                    <div class="mb-3">
                        <span class="text-xs font-semibold text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary))]/10 px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 group-hover:text-[rgb(var(--color-primary))] transition-colors">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </h3>
                    
                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>
                    
                    <div class="space-y-2 mb-4">
                        <?php if ($project['location']): ?>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span><?php echo htmlspecialchars($project['location']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($project['beneficiaries']): ?>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span><?php echo htmlspecialchars($project['beneficiaries']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php echo SITE_URL; ?>/project-detail.php?slug=<?php echo $project['slug']; ?>" 
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
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
