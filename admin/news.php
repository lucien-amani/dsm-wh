<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Supprimer
if (isset($_GET['delete'])) {
    $id_param = $_GET['delete'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        // Optionnel : récupérer le titre avant suppression pour le log
        $item = $pdo->prepare("SELECT title FROM news WHERE id = ?");
        $item->execute([$id]);
        $title = $item->fetchColumn() ?: "ID $id";

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        logAdminAction($_SESSION['admin_id'], "suppression article : $title");
        header('Location: ' . SITE_URL . '/admin/actualites?msg=deleted');
        exit;
    }
}

// Action: Toggle Publication
if (isset($_GET['toggle'])) {
    $id_param = $_GET['toggle'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE news SET published = 1 - published WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: ' . SITE_URL . '/admin/actualites?msg=updated');
        exit;
    }
}

$news_list = $pdo->query("SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC")->fetchAll();
$current_page = 'news';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités - DSM ADMIN</title>
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

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-24 flex items-center justify-between px-8 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Gestion des <span class="text-emerald-600">Actualités</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Archive & Publication</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=tweet" class="bg-[#1da1f2] hover:bg-[#1a91da] text-white px-6 py-4 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-[#1da1f2]/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    Tweet
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=linkedin" class="bg-[#0a66c2] hover:bg-[#004182] text-white px-6 py-4 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-[#0a66c2]/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    LinkedIn
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier?type=youtube" class="bg-[#ff0000] hover:bg-[#cc0000] text-white px-6 py-4 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-[#ff0000]/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    YouTube
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-4 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-600/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvel Article
                </a>
            </div>
        </header>

        <div class="p-8 space-y-8 animate-fade-in">
            <!-- Table Container -->
            <div class="glass-panel rounded-[3rem] shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aperçu</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Informations</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Engagement</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <?php foreach($news_list as $news): ?>
                        <tr class="hover:bg-emerald-50/10 dark:hover:bg-emerald-900/5 transition-all group">
                            <!-- Image Preview -->
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="w-20 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-500 border border-slate-200 dark:border-slate-700">
                                    <?php if($news['image']): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $news['image']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Info -->
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1 min-w-[200px]">
                                    <span class="font-extrabold text-slate-900 dark:text-white line-clamp-1 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($news['title']); ?></span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-md"><?php echo htmlspecialchars($news['category'] ?: 'Général'); ?></span>
                                        <span class="text-[10px] font-bold text-slate-400 lowercase">le <?php echo date('d/m/y', strtotime($news['created_at'])); ?> par <span class="text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($news['author_name'] ?: 'Admin'); ?></span></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Engagement / Stats -->
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex flex-col items-center p-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl min-w-[80px]">
                                    <span class="text-lg font-black text-slate-900 dark:text-white leading-none"><?php echo $news['views']; ?></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">Vues</span>
                                </div>
                            </td>

                            <!-- Publication Toggle -->
                            <td class="px-8 py-6 text-center">
                                <a href="?toggle=<?php echo $hashids->encode($news['id']); ?>" class="inline-flex items-center gap-2 group/status px-3 py-1.5 rounded-full border transition-all <?php echo $news['published'] ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500'; ?>">
                                    <div class="w-2 h-2 rounded-full <?php echo $news['published'] ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50' : 'bg-slate-400'; ?> transition-all animate-pulse"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $news['published'] ? 'En ligne' : 'Brouillon'; ?></span>
                                </a>
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="<?php echo SITE_URL; ?>/admin/actualites/modifier/<?php echo $hashids->encode($news['id']); ?>" class="w-11 h-11 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-lg hover:shadow-blue-500/20 border border-blue-500/10" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <a href="?delete=<?php echo $hashids->encode($news['id']); ?>" onclick="event.preventDefault(); showConfirm('Confirmer la suppression ?', () => window.location.href=this.href);" class="w-11 h-11 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($news_list)): ?>
                            <tr><td colspan="5" class="p-20 text-center text-slate-400 italic font-bold">Aucun article dans vos archives.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
