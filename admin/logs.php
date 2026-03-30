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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 transition-all duration-300">
        <header class="h-24 flex items-center justify-between px-8 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Journaux <span class="text-indigo-600">d'activité</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Logs système - 30 Jours Max</p>
            </div>
        </header>

        <div class="p-8 space-y-8 animate-fade-in">

            <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm mb-8">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6">Logs <span class="text-indigo-600">Récents</span></h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left bg-white/50 dark:bg-slate-900/50 rounded-2xl overflow-hidden">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-4">Utilisateur</th>
                                <th class="px-6 py-4">Rôle</th>
                                <th class="px-6 py-4">Action</th>
                                <th class="px-6 py-4">Adresse IP</th>
                                <th class="px-6 py-4">Date/Heure</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium">Aucun log récent.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($logs as $log): ?>
                                <tr class="hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($log['full_name']); ?></td>
                                    <td class="px-6 py-4 text-xs font-bold text-slate-500 uppercase"><?php echo htmlspecialchars($log['role']); ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($log['action'] === 'connexion'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div> Login
                                            </span>
                                        <?php elseif ($log['action'] === 'deconnexion'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-rose-100 dark:bg-rose-900/30 text-rose-600">
                                                <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Logout
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                <?php echo htmlspecialchars($log['action']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400"><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- EXPORTS -->
            <div class="glass-panel p-8 rounded-[2.5rem] shadow-sm">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter mb-6">Archives <span class="text-emerald-600">Automatiques (> 30 jous)</span></h3>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6 max-w-2xl text-balance">
                    Les journaux plus vieux que 30 jours sont automatiquement exportés ici au format JSON et supprimés de la base de données pour en préserver les performances.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($exports)): ?>
                        <div class="col-span-full text-center py-8 text-slate-500 font-medium">Aucun export disponible pour le moment.</div>
                    <?php else: ?>
                        <?php foreach($exports as $export): ?>
                            <div class="bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl flex items-center justify-between hover:shadow-xl hover:border-emerald-500/30 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white truncate"><?php echo htmlspecialchars($export['filename']); ?></span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?php echo date('d/m/Y H:i', strtotime($export['created_at'])); ?></span>
                                    </div>
                                </div>
                                <a href="?download=<?php echo urlencode($export['filename']); ?>" class="p-3 bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-110 active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </main>

</body>
</html>
