<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mail_from_name'])) {
        $mail_name = trim($_POST['mail_from_name']);
        
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES ('mail_from_name', :val1) ON DUPLICATE KEY UPDATE key_value = :val2");
        $stmt->execute([':val1' => $mail_name, ':val2' => $mail_name]);
        
        logAdminAction($_SESSION['admin_id'], "modification paramètres : mail_from_name -> $mail_name");
        $message = 'Le nom d\'expéditeur a été mis à jour avec succès !';
    }
}

// Récupérer la valeur actuelle
$stmt = $pdo->prepare("SELECT key_value FROM settings WHERE key_name = 'mail_from_name'");
$stmt->execute();
$current_mail_name = $stmt->fetchColumn() ?: 'supportdynamiquesamymagadju';

$current_page = 'settings';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres Généraux - DSM ADMIN</title>
    <link rel="stylesheet" href="/dsm/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { background: rgba(16, 6, 209, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(0, 255, 115, 0.5); }
        .dark .glass-panel { background: rgba(26, 64, 151, 0.6); border: 1px solid rgba(255, 255, 255, 1)}
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 overflow-hidden">
        <header class="h-24 sticky top-0 z-40 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 lg:px-12">
            <div>
                <h1 class="text-2xl font-black text-slate-500 dark:text-white uppercase tracking-tighter">Paramètres <span class="text-indigo-600">Généraux</span></h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em] mt-1">Configuration du système</p>
            </div>
        </header>

        <div class="p-8 lg:p-12 max-w-4xl">
            <?php if ($message): ?>
                <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center gap-3 animate-slide-in">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-bold text-sm"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm">
                <form action="" method="POST" class="space-y-8">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight mb-2">Configuration des Emails</h3>
                        <p class="text-sm text-slate-500 mb-6">Modifiez le nom qui apparaît comme expéditeur lors de l'envoi d'e-mails depuis la plateforme (contact, newsletter, etc.).</p>
                        
                        <div class="space-y-2">
                            <label for="mail_from_name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de l'expéditeur</label>
                            <input type="text" name="mail_from_name" id="mail_from_name" required 
                                value="<?php echo htmlspecialchars($current_mail_name); ?>"
                                class="w-full px-6 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all font-medium"
                                placeholder="Ex: Support Samy Magadju">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 uppercase tracking-widest text-xs">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-12 p-8 rounded-[2rem] bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/50">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-indigo-600/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-indigo-900 dark:text-indigo-400 uppercase tracking-tight text-sm mb-1">Information</h4>
                        <p class="text-xs text-indigo-800/70 dark:text-indigo-400/70 leading-relaxed font-medium">
                            Cette modification s'applique instantanément à tous les e-mails envoyés par le système (Formulaire de contact, confirmation d'abonnement à la newsletter, etc.).
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm">
                <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight mb-2">Lien d'envoi Newsletter</h3>
                <p class="text-sm text-slate-500 mb-6">URL directe vers la page d'expédition de la newsletter (après sélection des abonnés, actions, projets).</p>
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <input type="text" readonly value="http://localhost/dsm/admin/newsletter_send.php" 
                        class="w-full md:flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-600 dark:text-slate-400 font-bold focus:outline-none cursor-text truncate text-sm">
                    <a href="/dsm/admin/newsletter_send.php" target="_blank" class="w-full md:w-auto px-8 py-4 bg-amber-600 text-white font-black rounded-2xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20 active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ouvrir la page
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
