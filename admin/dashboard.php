<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Stats robustes
$news_count = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$projects_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$pending_comments_count = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$total_views = $pdo->query("SELECT SUM(views) FROM news")->fetchColumn() ?: 0;

// Activités récentes
$recent_actions = $pdo->query("
    (SELECT title, 'news' as type, created_at FROM news)
    UNION
    (SELECT title, 'project' as type, created_at FROM projects)
    ORDER BY created_at DESC LIMIT 8
")->fetchAll();

$current_page = 'dashboard';

// Extraction des données pour le graphique (7 derniers jours)
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d M', strtotime($date));
    $chart_labels[] = $display_date;
    
    // Compter l'activité totale pour ce jour (News + Projets + Messages + Commentaires)
    $q = "SELECT 
            (SELECT COUNT(*) FROM news WHERE DATE(created_at) = '$date') +
            (SELECT COUNT(*) FROM projects WHERE DATE(created_at) = '$date') +
            (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = '$date') +
            (SELECT COUNT(*) FROM comments WHERE DATE(created_at) = '$date') as total";
    $count = $pdo->query($q)->fetchColumn();
    $chart_data[] = $count;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super-Admin - DSM</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { 
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 transition-all duration-300">
        <!-- Header -->
        <header class="h-24 flex items-center justify-between px-8 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Tableau de <span class="text-emerald-600">Bord</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Statistiques Globales</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center gap-2 pr-4 shadow-sm">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold">
                        <?php echo date('d'); ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('F'); ?></span>
                        <span class="text-sm font-black text-slate-900 dark:text-white"><?php echo date('Y'); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrolling Content -->
        <div class="p-8 space-y-8 animate-fade-in">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Card: News -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 transition-all group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex flex-col gap-4">
                        <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:rotate-12 transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Actualités</p>
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter"><?php echo $news_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Stat Card: Projects -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex flex-col gap-4">
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 group-hover:rotate-12 transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Projets</p>
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter"><?php echo $projects_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Stat Card: Comments -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 transition-all group overflow-hidden relative">
                    <a href="comments.php" class="absolute inset-0 z-10"></a>
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex flex-col gap-4">
                        <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 group-hover:rotate-12 transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Commentaires</p>
                                <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter"><?php echo count($pdo->query("SELECT id FROM comments")->fetchAll()); ?></h3>
                            </div>
                            <?php if($pending_comments_count > 0): ?>
                                <span class="bg-amber-500 text-white text-[10px] font-black px-2 py-1 rounded-lg mb-1 animate-pulse"><?php echo $pending_comments_count; ?> À MODÉRER</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Stat Card: Messages -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:shadow-rose-500/10 transition-all group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex flex-col gap-4">
                        <div class="w-14 h-14 bg-rose-100 dark:bg-rose-900/30 rounded-2xl flex items-center justify-center text-rose-600 group-hover:rotate-12 transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Messages</p>
                            <h3 class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter"><?php echo $messages_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-panel p-10 rounded-[3rem] shadow-sm">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Évolution de <span class="text-emerald-600">Visibilité</span></h3>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">7 derniers jours</span>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>

                <div class="glass-panel p-10 rounded-[3rem] shadow-sm">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Répartition <span class="text-blue-600">Contenu</span></h3>
                    </div>
                    <div class="h-[300px] flex items-center justify-center">
                        <canvas id="contentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="glass-panel rounded-[3rem] overflow-hidden">
                <div class="p-10 border-b border-slate-100 dark:border-slate-800/50 flex justify-between items-center bg-white/30 dark:bg-slate-900/30">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Activités <span class="text-emerald-600">Récents</span></h3>
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800/50">
                                <th class="px-10 py-6">Élément</th>
                                <th class="px-10 py-6">Type</th>
                                <th class="px-10 py-6">Date de publication</th>
                                <th class="px-10 py-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            <?php foreach($recent_actions as $action): ?>
                            <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                                <td class="px-10 py-6 font-bold text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($action['title']); ?></td>
                                <td class="px-10 py-6 uppercase text-[10px] font-black tracking-[0.2em] <?php echo $action['type'] === 'news' ? 'text-emerald-500' : 'text-blue-500'; ?>"><?php echo $action['type']; ?></td>
                                <td class="px-10 py-6 text-slate-500 dark:text-slate-400 text-sm font-medium"><?php echo date('d M Y, H:i', strtotime($action['created_at'])); ?></td>
                                <td class="px-10 py-6 text-right">
                                    <a href="<?php echo SITE_URL; ?>/admin/<?php echo $action['type'] === 'news' ? 'actualites' : 'projets'; ?>" class="text-emerald-600 font-black text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all hover:underline">Gérer →</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Charts Data
        const ctxViews = document.getElementById('viewsChart').getContext('2d');
        new Chart(ctxViews, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Activité globale',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                    y: { 
                        grid: { color: 'rgba(148, 163, 184, 0.1)' }, 
                        ticks: { 
                            color: '#94a3b8',
                            stepSize: 1,
                            beginAtZero: true
                        } 
                    }
                }
            }
        });

        const ctxContent = document.getElementById('contentChart').getContext('2d');
        new Chart(ctxContent, {
            type: 'doughnut',
            data: {
                labels: ['Actualités', 'Projets'],
                datasets: [{
                    data: [<?php echo $news_count; ?>, <?php echo $projects_count; ?>],
                    backgroundColor: ['#10b981', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { weight: 'bold' } } } },
                cutout: '80%'
            }
        });
    </script>
</body>
</html>
