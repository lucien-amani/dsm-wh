<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

// Seul Superadmin et Admin ont accès
if ($_SESSION['admin_role'] !== 'Superadmin' && $_SESSION['admin_role'] !== 'Admin') {
    header('Location: ' . SITE_URL . '/admin/tableau-de-bord');
    exit;
}

$pdo = getDBConnection();

// --- 1. AUTO EXPORT LOGS OLDER THAN 30 DAYS ---
try {
    $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
    $stmt_old = $pdo->prepare("SELECT al.*, u.full_name, u.role, u.email FROM admin_logs al JOIN users u ON al.user_id = u.id WHERE al.created_at < :t_date");
    $stmt_old->execute([':t_date' => $thirty_days_ago]);
    $old_logs = $stmt_old->fetchAll();

    if (count($old_logs) > 0) {
        $export_dir = __DIR__ . '/exports/logs';
        if (!is_dir($export_dir)) {
            mkdir($export_dir, 0777, true);
        }

        $filename = 'logs_export_' . date('Y_m_d_H_i_s') . '.json';
        $filepath = $export_dir . '/' . $filename;
        
        file_put_contents($filepath, json_encode($old_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Enregistrer l'export dans la base de données
        $stmt_exp = $pdo->prepare("INSERT INTO log_exports (filename) VALUES (:fname)");
        $stmt_exp->execute([':fname' => $filename]);
        
        // Supprimer les vieux logs de la base
        $stmt_del = $pdo->prepare("DELETE FROM admin_logs WHERE created_at < :t_date");
        $stmt_del->execute([':t_date' => $thirty_days_ago]);
    }
} catch (Exception $e) {
    // Si la table n'existe pas ou erreur, on ignore l'erreur
}


// --- 2. TRAITEMENT DU TÉLÉCHARGEMENT ---
if (isset($_GET['download'])) {
    $dl_file = basename($_GET['download']);
    $dl_path = __DIR__ . '/exports/logs/' . $dl_file;
    if (file_exists($dl_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="'.$dl_file.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($dl_path));
        readfile($dl_path);
        exit;
    }
}


// --- 3. RÉCUPÉRATION DES LOGS COURANTS ---
$logs = [];
try {
    $stmt = $pdo->query("SELECT al.*, u.full_name, u.role FROM admin_logs al JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 100");
    $logs = $stmt->fetchAll();
} catch(Exception $e) {}


// --- 4. RÉCUPÉRATION DES EXPORTS ---
$exports = [];
try {
    $stmt_e = $pdo->query("SELECT * FROM log_exports ORDER BY created_at DESC");
    $exports = $stmt_e->fetchAll();
} catch(Exception $e) {}

$current_page = 'logs';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journaux d'activité - DSM Admin</title>
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

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-24 flex items-center justify-between px-8 lg:px-12 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Journaux <span class="text-emerald-600">d'activité</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Traçabilité & Sécurité</p>
            </div>
            
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-200 dark:border-emerald-800/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </header>

        <div class="p-8 lg:p-12 space-y-12 animate-fade-in">
            
            <!-- Logs Récents Section -->
            <section>
                <div class="flex items-center justify-between mb-6 px-4">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Activités Récentes</h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">100 derniers logs</span>
                </div>

                <div class="glass-panel rounded-[2.5rem] overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20">
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilisateur</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Action</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Détails de l'IP</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Date & Heure</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="4" class="px-8 py-12 text-center text-slate-400 italic font-medium uppercase tracking-widest text-[10px]">Aucun log récent.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($logs as $log): ?>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all group">
                                        <td class="px-8 py-5">
                                            <div class="flex flex-col">
                                                <span class="font-black text-slate-900 dark:text-white uppercase tracking-tighter text-sm"><?php echo htmlspecialchars($log['full_name']); ?></span>
                                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest"><?php echo htmlspecialchars($log['role']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex justify-center">
                                                <?php if ($log['action'] === 'connexion'): ?>
                                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-500/20 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Login
                                                    </span>
                                                <?php elseif ($log['action'] === 'deconnexion'): ?>
                                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-slate-50 dark:bg-slate-800/50 text-slate-500 border border-slate-500/10 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Logout
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 border border-indigo-500/20">
                                                        <?php echo htmlspecialchars($log['action']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md"><?php echo htmlspecialchars($log['ip_address']); ?></span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tighter"><?php echo date('d/m/Y', strtotime($log['created_at'])); ?></span>
                                            <span class="block text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Archives Section -->
            <section>
                <div class="flex items-center justify-between mb-6 px-4">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Archives Automatiques</h3>
                    <div class="px-3 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-500/20 rounded-full text-[9px] font-black text-amber-600 uppercase tracking-widest">Rétention 30 jours</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php if (empty($exports)): ?>
                        <div class="col-span-full glass-panel rounded-[2rem] p-12 text-center text-slate-400 italic font-medium uppercase tracking-widest text-[10px]">
                            Aucune archive disponible.
                        </div>
                    <?php else: ?>
                        <?php foreach($exports as $export): ?>
                            <div class="glass-panel p-6 rounded-[2rem] flex items-center justify-between group hover:border-emerald-500/30 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 dark:group-hover:bg-emerald-900/20 transition-all flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate"><?php echo str_replace('logs_export_', '', $export['filename']); ?></span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"><?php echo date('d M Y, H:i', strtotime($export['created_at'])); ?></span>
                                    </div>
                                </div>
                                <a href="?download=<?php echo urlencode($export['filename']); ?>" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-lg hover:shadow-emerald-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

        </div>
    </main>

</body>
</html>
