<?php
require_once 'config/database.php';

// Récupérer le slug du projet
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    header('Location: ' . SITE_URL . '/projects.php');
    exit;
}

$pdo = getDBConnection();

// Récupérer le projet
$stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = :slug AND published = 1");
$stmt->execute([':slug' => $slug]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: ' . SITE_URL . '/projects.php');
    exit;
}

// Récupérer les projets similaires
$stmt = $pdo->prepare("SELECT * FROM projects WHERE published = 1 AND id != :id AND (category = :category OR location = :location) ORDER BY created_at DESC LIMIT 3");
$stmt->execute([':id' => $project['id'], ':category' => $project['category'], ':location' => $project['location']]);
$related_projects = $stmt->fetchAll();

$current_page = 'projects';
$page_title = htmlspecialchars($project['title']) . ' - Ir. Samy Magadju';
$page_description = htmlspecialchars($project['description']);

include 'includes/header.php';
?>

<!-- Project Header -->
<section class="bg-gradient-to-r from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8 text-sm">
            <ol class="flex items-center gap-2 text-white/70">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="hover:text-white">Accueil</a></li>
                <li>›</li>
                <li><a href="<?php echo SITE_URL; ?>/projects.php" class="hover:text-white">Projets</a></li>
                <li>›</li>
                <li class="text-white"><?php echo htmlspecialchars($project['title']); ?></li>
            </ol>
        </nav>

        <div class="flex flex-wrap items-center gap-4 mb-6">
            <?php
            $statusColors = [
                'En cours' => 'bg-blue-500',
                'Terminé' => 'bg-green-500',
                'Planifié' => 'bg-orange-500'
            ];
            $statusColor = $statusColors[$project['status']] ?? 'bg-gray-500';
            ?>
            <span class="<?php echo $statusColor; ?> text-white text-sm font-bold px-4 py-2 rounded-full">
                <?php echo htmlspecialchars($project['status']); ?>
            </span>
            <?php if ($project['category']): ?>
            <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full">
                <?php echo htmlspecialchars($project['category']); ?>
            </span>
            <?php endif; ?>
        </div>

        <h1 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo htmlspecialchars($project['title']); ?>
        </h1>

        <p class="text-xl text-white/90 max-w-4xl">
            <?php echo htmlspecialchars($project['description']); ?>
        </p>
    </div>
</section>

<!-- Project Content -->
<section class="py-16 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Featured Image -->
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
                <figure class="mb-12">
                    <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl">
                        <img src="<?php echo $project_image; ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>"
                             class="w-full h-full object-cover">
                    </div>
                </figure>

                <!-- Description -->
                <div class="prose prose-lg max-w-none">
                    <?php echo $project['content']; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-8 sticky top-24 transition-colors">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Détails du Projet</h3>
                    
                    <div class="space-y-6">
                        <?php if ($project['location']): ?>
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                Localisation
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo htmlspecialchars($project['location']); ?></p>
                        </div>
                        <?php endif; ?>

                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Statut
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo htmlspecialchars($project['status']); ?></p>
                        </div>

                        <?php if ($project['start_date']): ?>
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date de début
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo formatDateFR($project['start_date']); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($project['end_date']): ?>
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date de fin
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo formatDateFR($project['end_date']); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($project['budget']): ?>
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Budget
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo htmlspecialchars($project['budget']); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($project['beneficiaries']): ?>
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 text-[rgb(var(--color-primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Bénéficiaires
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 pl-7"><?php echo htmlspecialchars($project['beneficiaries']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Share -->
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Partager ce projet</h4>
                        <div class="flex gap-2">
                            <button onclick="shareOnFacebook()" class="flex-1 p-3 bg-[#1877f2] text-white rounded-lg hover:bg-[#166fe5] transition-colors">
                                <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </button>
                            <button onclick="shareOnTwitter()" class="flex-1 p-3 bg-[#1da1f2] text-white rounded-lg hover:bg-[#1a91da] transition-colors">
                                <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </button>
                            <button onclick="shareOnWhatsApp()" class="flex-1 p-3 bg-[#25d366] text-white rounded-lg hover:bg-[#20ba5a] transition-colors">
                                <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Projects -->
<?php if (!empty($related_projects)): ?>
<section class="py-16 bg-gray-50 dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Projets similaires</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related_projects as $related): ?>
            <article class="card group">
                <a href="<?php echo SITE_URL; ?>/project-detail.php?slug=<?php echo $related['slug']; ?>" 
                   class="block aspect-video bg-gray-200 overflow-hidden relative">
                    <?php if ($related['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/' . $related['image']; ?>" 
                             alt="<?php echo htmlspecialchars($related['title']); ?>"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-[rgb(var(--color-primary))] to-[rgb(var(--color-primary-dark))] flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <?php
                    $statusColor = $statusColors[$related['status']] ?? 'bg-gray-500';
                    ?>
                    <span class="absolute top-3 right-3 <?php echo $statusColor; ?> text-white text-xs font-bold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($related['status']); ?>
                    </span>
                </a>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 group-hover:text-[rgb(var(--color-primary))] transition-colors line-clamp-2">
                        <?php echo htmlspecialchars($related['title']); ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                        <?php echo htmlspecialchars($related['description']); ?>
                    </p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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
