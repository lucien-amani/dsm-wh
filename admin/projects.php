<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Supprimer
if (isset($_GET['delete'])) {
    $id_param = $_GET['delete'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $item = $pdo->prepare("SELECT title FROM projects WHERE id = ?");
        $item->execute([$id]);
        $title = $item->fetchColumn() ?: "ID $id";

        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        logAdminAction($_SESSION['admin_id'], "suppression projet : $title");
        header('Location: ' . SITE_URL . '/admin/projets?msg=deleted');
        exit;
    }
}

// Action: Toggle Publication
if (isset($_GET['toggle'])) {
    $id_param = $_GET['toggle'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE projects SET published = 1 - published WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: ' . SITE_URL . '/admin/projets?msg=updated');
        exit;
    }
}

$projects = $pdo->query("SELECT p.*, u.full_name as author_name FROM projects p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC")->fetchAll();
$current_page = 'projects';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-panel { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <!-- Header -->
        <?php 
        $header_title = 'Gestion des <span class="text-blue-600">Projets</span>';
        $header_subtitle = 'Portfolio & Réalisations';
        $header_actions = '
            <a href="'.SITE_URL.'/admin/projets/modifier" class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 lg:px-6 lg:py-3 rounded-xl font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg shadow-blue-600/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Nouveau Projet</span>
            </a>';
        include 'includes/header.php'; 
        ?>

        <div class="p-8 space-y-8 animate-fade-in">
            <!-- Table Container -->
            <div class="glass-panel rounded-[3rem] shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aperçu</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Détails du Projet</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Visibilité</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <?php foreach($projects as $proj): ?>
                        <tr class="hover:bg-blue-50/10 dark:hover:bg-blue-900/5 transition-all group">
                            <!-- Image Preview -->
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="w-20 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-500 border border-slate-200 dark:border-slate-700">
                                    <?php if($proj['image']): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $proj['image']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">NO-IMG</div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Info -->
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1 min-w-[250px]">
                                    <span class="font-extrabold text-slate-900 dark:text-white line-clamp-1 group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($proj['title']); ?></span>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-md"><?php echo htmlspecialchars($proj['category'] ?: 'Général'); ?></span>
                                        <span class="text-[9px] font-bold text-slate-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <?php echo htmlspecialchars($proj['location'] ?: 'Non spécifié'); ?>
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-400">par <span class="text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($proj['author_name'] ?: 'Admin'); ?></span></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Project Status Badge -->
                            <td class="px-8 py-6 text-center">
                                <?php 
                                    $status_colors = [
                                        'En cours' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 border-amber-500/20',
                                        'Terminé' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 border-emerald-500/20',
                                        'Planifié' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 border-blue-500/20'
                                    ];
                                    $color = $status_colors[$proj['status']] ?? 'bg-slate-100 text-slate-600';
                                ?>
                                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border <?php echo $color; ?>">
                                    <?php echo htmlspecialchars($proj['status']); ?>
                                </span>
                            </td>

                            <!-- Publication Toggle -->
                            <td class="px-8 py-6 text-center">
                                <a href="?toggle=<?php echo $hashids->encode($proj['id']); ?>" class="inline-flex items-center gap-2 group/status px-3 py-1.5 rounded-full border transition-all <?php echo $proj['published'] ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500'; ?>">
                                    <div class="w-2 h-2 rounded-full <?php echo $proj['published'] ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50' : 'bg-slate-400'; ?> transition-all animate-pulse"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $proj['published'] ? 'Public' : 'Privé'; ?></span>
                                </a>
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="<?php echo SITE_URL; ?>/admin/projets/modifier/<?php echo $hashids->encode($proj['id']); ?>" class="w-11 h-11 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-lg hover:shadow-blue-500/20 border border-blue-500/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <a href="?delete=<?php echo $hashids->encode($proj['id']); ?>" onclick="return confirm('Supprimer ce projet définitivement ?')" class="w-11 h-11 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
