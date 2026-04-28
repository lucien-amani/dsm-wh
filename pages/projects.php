<?php
// pages/projects_list.php
// Ce fichier remplace l'ancien projects.php à la racine

$current_page = 'projects';
$page_title = 'Projets - Dynamique Samy Magadju/Wema ni Hakiba';
$page_description = 'Découvrez les projets d\'impact social, d\'infrastructure et de développement portés par la Dynamique Samy Magadju/Wema ni Hakiba.';

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

// Récupérer les statistiques réelles (incluant tous les statuts possibles)
$all_statuses = ['En cours', 'Terminé', 'Planifié'];
$stats = [];
foreach ($all_statuses as $s) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE status = ? AND published = 1");
    $stmt->execute([$s]);
    $stats[$s] = $stmt->fetchColumn();
}
$total_projects = array_sum($stats);

include 'includes/header.php';
?>

<!-- Barre de Filtres : Version Mobile (Remplace le header) -->
<section id="projects-filter-bar-mobile" class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-4 sticky top-0 z-[60] shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-4 overflow-x-auto no-scrollbar whitespace-nowrap">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 flex-shrink-0">Filtrer :</span>
            <div class="flex gap-2">
                <a href="<?php echo $router->generate('projects_list'); ?>" class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex-shrink-0 <?php echo !$status ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'; ?>">Tous</a>
                <a href="<?php echo $router->generate('projects_list'); ?>?status=En cours" class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex-shrink-0 <?php echo $status === 'En cours' ? 'bg-blue-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'; ?>">En cours</a>
                <a href="<?php echo $router->generate('projects_list'); ?>?status=Terminé" class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex-shrink-0 <?php echo $status === 'Terminé' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'; ?>">Terminés</a>
                <a href="<?php echo $router->generate('projects_list'); ?>?status=Planifié" class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex-shrink-0 <?php echo $status === 'Planifié' ? 'bg-amber-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'; ?>">Planifiés</a>
            </div>
        </div>
    </div>
</section>

<!-- Page Header (Bureau & Mobile) -->
<section class="bg-gradient-to-br from-slate-900 via-[rgb(var(--color-primary-dark))] to-[rgb(var(--color-primary))] text-white py-24 relative overflow-hidden">
    <!-- Décorations SVG -->
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute top-0 right-0 w-96 h-96 -mr-20 -mt-20" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="white" fill="none" stroke-width="0.5" stroke-dasharray="2 2"/></svg>
        <svg class="absolute bottom-0 left-0 w-64 h-64 -ml-10 -mb-10 opacity-20" viewBox="0 0 100 100"><rect x="20" y="20" width="60" height="60" stroke="white" fill="none" stroke-width="0.5" transform="rotate(45 50 50)"/></svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-7xl font-black mb-6 uppercase tracking-tighter">Nos <span class="text-white/70">Projets</span></h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed">
                Des réalisations concrètes au service du développement durable et du bien-être de nos communautés au Sud-Kivu.
            </p>
        </div>

        <!-- Stats Premium -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <a href="<?php echo $router->generate('projects_list'); ?>" class="group bg-white/10 backdrop-blur-md border border-white/10 rounded-[2rem] p-6 text-center hover:bg-white/20 transition-all duration-500 <?php echo !$status ? 'ring-4 ring-white/30 bg-white/25' : ''; ?>">
                <div class="text-4xl md:text-5xl font-black mb-1"><?php echo $total_projects; ?></div>
                <div class="text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-white/70">Projets Totaux</div>
            </a>
            <a href="<?php echo $router->generate('projects_list'); ?>?status=En cours" class="group bg-blue-500/10 backdrop-blur-md border border-blue-400/20 rounded-[2rem] p-6 text-center hover:bg-blue-500/20 transition-all duration-500 <?php echo $status === 'En cours' ? 'ring-4 ring-blue-400/50 bg-blue-500/30' : ''; ?>">
                <div class="text-4xl md:text-5xl font-black mb-1 text-blue-400"><?php echo $stats['En cours']; ?></div>
                <div class="text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-blue-300/70">En cours</div>
            </a>
            <a href="<?php echo $router->generate('projects_list'); ?>?status=Terminé" class="group bg-emerald-500/10 backdrop-blur-md border border-emerald-400/20 rounded-[2rem] p-6 text-center hover:bg-emerald-500/20 transition-all duration-500 <?php echo $status === 'Terminé' ? 'ring-4 ring-emerald-400/50 bg-emerald-500/30' : ''; ?>">
                <div class="text-4xl md:text-5xl font-black mb-1 text-emerald-400"><?php echo $stats['Terminé']; ?></div>
                <div class="text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-emerald-300/70">Terminés</div>
            </a>
            <a href="<?php echo $router->generate('projects_list'); ?>?status=Planifié" class="group bg-amber-500/10 backdrop-blur-md border border-amber-400/20 rounded-[2rem] p-6 text-center hover:bg-amber-500/20 transition-all duration-500 <?php echo $status === 'Planifié' ? 'ring-4 ring-amber-400/50 bg-amber-500/30' : ''; ?>">
                <div class="text-4xl md:text-5xl font-black mb-1 text-amber-400"><?php echo $stats['Planifié']; ?></div>
                <div class="text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-amber-300/70">Planifiés</div>
            </a>
        </div>
    </div>
</section>

<!-- Barre de Filtres : Version Bureau (Sous le header) -->
<section class="hidden lg:block bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-6 sticky top-[80px] z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Filtrer par :</span>
                <div class="flex gap-2">
                    <a href="<?php echo $router->generate('projects_list'); ?>" class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest bg-slate-900 text-white shadow-lg shadow-slate-900/20">Tous</a>
                    <?php if($status): ?>
                        <span class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest bg-emerald-600 text-white shadow-lg flex items-center gap-2">
                            Statut: <?php echo htmlspecialchars($status); ?>
                            <a href="<?php echo $router->generate('projects_list'); ?>">✕</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]"><?php echo count($projects); ?> Projet(s)</div>
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
                <?php if (!empty($project['embed_code'])): ?>
                    <div class="block aspect-video bg-gray-200 overflow-hidden relative flex items-center justify-center p-2">
                        <?php echo parseEmbedCode($project['embed_code']); ?>
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 z-10 pointer-events-none">
                            <?php
                            $statusColors = [
                                'En cours' => 'bg-blue-500',
                                'Terminé' => 'bg-green-500',
                                'Planifié' => 'bg-orange-500'
                            ];
                            $statusColor = $statusColors[$project['status']] ?? 'bg-gray-500';
                            ?>
                            <span class="<?php echo $statusColor; ?> text-white text-xs font-bold px-3 py-1 rounded-full pointer-events-auto">
                                <?php echo htmlspecialchars($project['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo getUrl('projects', $project['id'], $project['slug'], $project['nanoid'] ?? null); ?>" class="block aspect-video bg-gray-200 overflow-hidden relative">
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
                    </a>
                <?php endif; ?>

                <div class="p-6">
                    <?php if ($project['category']): ?>
                    <div class="mb-3">
                        <span class="text-xs font-semibold text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary))]/10 px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 group-hover:text-[rgb(var(--color-primary))] transition-colors">
                        <a href="<?php echo getUrl('projects', $project['id'], $project['slug'], $project['nanoid'] ?? null); ?>">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </a>
                    </h3>

                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>

                    <div class="space-y-2 mb-4">
                        <?php if ($project['location']): ?>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2-2L7.173 14.657a8 8 0 1111.314 0z"/>
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
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
