<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Supprimer
if (isset($_GET['delete'])) {
    $id_param = $_GET['delete'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: ' . SITE_URL . '/admin/newsletter?msg=deleted');
        exit;
    }
}

// Action: Toggle Statut
if (isset($_GET['toggle'])) {
    $id_param = $_GET['toggle'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = IF(status='active', 'unsubscribed', 'active') WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: ' . SITE_URL . '/admin/newsletter?msg=updated');
        exit;
    }
}

$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll();
$current_page = 'newsletter';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnés Newsletter - DSM ADMIN</title>
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
        <header class="h-24 sticky top-0 z-40 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 lg:px-12">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Gestion <span class="text-amber-600">Newsletter</span></h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em] mt-1">Gérer la liste des abonnés</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="<?php echo SITE_URL; ?>/admin/newsletter/envoyer" class="px-6 py-3 bg-amber-600 text-white font-black rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20 active:scale-95 uppercase tracking-widest text-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Envoyer une newsletter
                </a>
                <div class="hidden md:flex flex-col text-right">
                    <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tighter"><?php echo count($subscribers); ?></span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Abonnés total</span>
                </div>
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-xl flex items-center justify-center border border-amber-200 dark:border-amber-800/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </header>

        <div class="p-8 lg:p-12">
            <div class="glass-panel rounded-[3rem] overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-800/50">
                            <th class="px-10 py-6">E-mail</th>
                            <th class="px-10 py-6 text-center">Status</th>
                            <th class="px-10 py-6">Date d'inscription</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <?php foreach($subscribers as $sub): ?>
                        <tr class="hover:bg-amber-50/10 dark:hover:bg-amber-900/5 transition-all group">
                            <td class="px-10 py-6">
                                <span class="font-bold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($sub['email']); ?></span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <a href="?toggle=<?php echo $hashids->encode($sub['id']); ?>" class="inline-flex items-center gap-2 group/status px-3 py-1.5 rounded-full border transition-all <?php echo $sub['status'] === 'active' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500'; ?>">
                                    <div class="w-2 h-2 rounded-full <?php echo $sub['status'] === 'active' ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50' : 'bg-slate-400'; ?> transition-all animate-pulse"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $sub['status'] === 'active' ? 'Actif' : 'Désabonné'; ?></span>
                                </a>
                            </td>
                            <td class="px-10 py-6 text-slate-500 dark:text-slate-400 text-sm font-medium">
                                <?php echo date('d M Y, H:i', strtotime($sub['created_at'])); ?>
                            </td>
                            <td class="px-10 py-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="?delete=<?php echo $hashids->encode($sub['id']); ?>" onclick="return confirm('Supprimer cet abonné ?')" class="w-11 h-11 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($subscribers)): ?>
                            <tr><td colspan="4" class="p-20 text-center text-slate-400 italic font-bold">Aucun abonné pour le moment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
