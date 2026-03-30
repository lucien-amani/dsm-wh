<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Actions de modération
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        $status = $_GET['status'] ?? 'pending';
        header("Location: " . SITE_URL . "/admin/commentaires?msg=approved&status=$status");
        exit;
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $status = $_GET['status'] ?? 'pending';
        header("Location: " . SITE_URL . "/admin/commentaires?msg=rejected&status=$status");
        exit;
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $status = $_GET['status'] ?? 'pending';
        header("Location: " . SITE_URL . "/admin/commentaires?msg=deleted&status=$status");
        exit;
    }
}

// Filtrage
$status_filter = $_GET['status'] ?? 'pending';
$stmt = $pdo->prepare("
    SELECT c.*, n.title as news_title 
    FROM comments c 
    JOIN news n ON c.news_id = n.id 
    WHERE c.status = :status 
    ORDER BY c.created_at DESC
");
$stmt->execute([':status' => $status_filter]);
$comments = $stmt->fetchAll();

$current_page = 'comments';
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération - DSM ADMIN</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-panel { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        .dark .glass-panel { 
            background: rgba(15, 23, 42, 0.8); 
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        .status-tab {
            padding: 0.875rem 2rem;
            border-radius: 1rem;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0">
        <!-- Premium Header -->
        <header class="h-28 flex items-center justify-between px-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-600/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none">Modération <span class="text-emerald-600">Interact</span></h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] mt-2">Commentaires & Retours</p>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-3 px-6 py-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-sm">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">Live Moderation</span>
            </div>
        </header>

        <div class="p-10 space-y-10 animate-fade-in max-w-6xl mx-auto w-full">
            
            <!-- Navigation par Onglets (Visibilité Maximale) -->
            <div class="flex flex-wrap items-center gap-4 bg-white dark:bg-slate-900 p-3 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm">
                <a href="?status=pending" class="flex-1 sm:flex-none text-center px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    En Attente
                </a>
                <a href="?status=approved" class="flex-1 sm:flex-none text-center px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'approved' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    Approuvés
                </a>
                <a href="?status=rejected" class="flex-1 sm:flex-none text-center px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'rejected' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    Rejetés
                </a>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-8">
                <?php if (empty($comments)): ?>
                    <div class="glass-panel p-24 rounded-[4rem] text-center border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center">
                        <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] flex items-center justify-center mb-8 text-slate-200 dark:text-slate-700">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 5-8-5"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-2">Boîte de réception vide</h3>
                        <p class="text-slate-400 font-medium italic">Aucun commentaire n'est actuellement marqué comme « <?php echo $status_filter; ?> ».</p>
                    </div>
                <?php else: ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="glass-panel p-10 rounded-[3.5rem] flex flex-col lg:flex-row gap-10 items-start hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 group border border-white/40 dark:border-slate-800/50">
                            
                            <div class="flex-1 w-full space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-500/20 transform group-hover:-rotate-3 transition-transform">
                                            <?php echo strtoupper(substr($comment['user_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-lg text-slate-900 dark:text-white uppercase tracking-tighter leading-none mb-1"><?php echo htmlspecialchars($comment['user_name']); ?></h4>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('d M Y', strtotime($comment['created_at'])); ?></span>
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest"><?php echo date('H:i', strtotime($comment['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 line-clamp-1 max-w-[200px]"><?php echo htmlspecialchars($comment['news_title']); ?></span>
                                    </div>
                                </div>

                                <div class="relative">
                                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-emerald-500/30 rounded-full"></div>
                                    <p class="text-slate-700 dark:text-slate-300 text-[15px] leading-relaxed font-semibold italic pl-4">
                                        « <?php echo nl2br(htmlspecialchars($comment['content'])); ?> »
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex lg:flex-col gap-3 w-full lg:w-auto shrink-0 pt-2 lg:pt-0">
                                <?php if($comment['status'] !== 'approved'): ?>
                                <a href="?action=approve&id=<?php echo $comment['id']; ?>&status=<?php echo $status_filter; ?>" 
                                   class="flex-1 lg:flex-none px-8 py-4 bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-emerald-700 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 shadow-xl shadow-emerald-600/20 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Approuver
                                </a>
                                <?php endif; ?>

                                <?php if($comment['status'] !== 'rejected'): ?>
                                <a href="?action=reject&id=<?php echo $comment['id']; ?>&status=<?php echo $status_filter; ?>" 
                                   class="flex-1 lg:flex-none px-8 py-4 bg-amber-500 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-amber-600 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 shadow-xl shadow-amber-500/20 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rejeter
                                </a>
                                <?php endif; ?>

                                <button onclick="showConfirm('Attention ! Suppression définitive de ce message. Confirmer ?', () => window.location.href='?action=delete&id=<?php echo $comment['id']; ?>&status=<?php echo $status_filter; ?>')"
                                   class="flex-1 lg:flex-none px-8 py-4 bg-rose-50 dark:bg-rose-900/10 text-rose-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center gap-3 border border-rose-100 dark:border-rose-900/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>
</html>
