<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Supprimer
if (isset($_GET['delete'])) {
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
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
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
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
        <header class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50 transition-all">
            <div>
                <h1 class="text-xl lg:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate">Gestion <span class="text-amber-600">Newsletter</span></h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em] ml-1">Communauté & Abonnés</p>
            </div>
            
            <div class="flex items-center gap-6">
                <a href="<?php echo SITE_URL; ?>/admin/newsletter/envoyer" class="px-4 py-2 lg:px-6 lg:py-3 bg-amber-600 text-white font-black rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20 active:scale-95 uppercase tracking-widest text-[9px] lg:text-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span class="hidden sm:inline">Envoyer</span>
                </a>
                <div class="hidden sm:block text-right">
                    <span class="block text-lg font-black text-slate-900 dark:text-white leading-none"><?php echo count($subscribers); ?></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Abonnés</span>
                </div>
            </div>
        </header>

        <div class="p-8 lg:p-12 space-y-8 animate-fade-in">
            <div class="glass-panel rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">E-mail de l'abonné</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Date / Heure</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            <?php foreach($subscribers as $sub): ?>
                            <tr class="hover:bg-amber-50/10 dark:hover:bg-amber-900/5 transition-all group">
                                <td class="px-8 py-6">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm"><?php echo htmlspecialchars($sub['email']); ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-center">
                                        <a href="?toggle=<?php echo $hashids->encode($sub['id']); ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all 
                                            <?php echo $sub['status'] === 'active' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500'; ?>">
                                            <div class="w-1.5 h-1.5 rounded-full <?php echo $sub['status'] === 'active' ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50' : 'bg-slate-400'; ?>"></div>
                                            <span class="text-[9px] font-black uppercase tracking-widest"><?php echo $sub['status'] === 'active' ? 'Actif' : 'Désabonné'; ?></span>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400"><?php echo date('d/m/Y', strtotime($sub['created_at'])); ?></span>
                                    <span class="block text-[10px] font-medium text-slate-400"><?php echo date('H:i', strtotime($sub['created_at'])); ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-3 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <a href="?delete=<?php echo $hashids->encode($sub['id']); ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" onclick="event.preventDefault(); showConfirm('Supprimer cet abonné ?', () => window.location.href=this.href);" class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($subscribers)): ?>
                                <tr>
                                    <td colspan="4" class="p-20 text-center space-y-4">
                                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto text-slate-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <p class="text-slate-400 italic font-bold uppercase tracking-widest text-[10px]">Aucun abonné pour le moment.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
