<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$message = '';
$status = 'success';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = false;

    if (isset($_POST['mail_from_name'])) {
        $mail_name = trim($_POST['mail_from_name']);
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES ('mail_from_name', :val1) ON DUPLICATE KEY UPDATE key_value = :val2");
        $stmt->execute([':val1' => $mail_name, ':val2' => $mail_name]);
        $updated = true;
    }

    if (isset($_POST['membership_enabled'])) {
        $membership_status = ($_POST['membership_enabled'] == '1') ? '1' : '0';
        
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM settings WHERE key_name = 'membership_enabled'")->execute();
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES ('membership_enabled', :val)");
            $stmt->execute([':val' => $membership_status]);
            $pdo->commit();
            
            logAdminAction($_SESSION['admin_id'], "modification paramètres : membership_enabled -> $membership_status");
            $updated = true;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = "Erreur : " . $e->getMessage();
            $status = 'error';
        }
    }

    if ($updated && empty($message)) {
        $message = 'Paramètres enregistrés avec succès !';
        $status = 'success';
    }
}

// Récupérer les valeurs actuelles
$stmt = $pdo->prepare("SELECT key_value FROM settings WHERE key_name = 'mail_from_name'");
$stmt->execute();
$current_mail_name = $stmt->fetchColumn() ?: 'Support DSM-WH';

$stmt = $pdo->prepare("SELECT key_value FROM settings WHERE key_name = 'membership_enabled'");
$stmt->execute();
$membership_enabled = $stmt->fetchColumn() === '1';

$current_page = 'settings';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.6); }
        .dark .glass-panel { background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.05); }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        <!-- Header -->
        <?php 
        $header_title = 'Paramètres <span class="text-emerald-600">Généraux</span>';
        $header_subtitle = 'Configuration du système';
        include 'includes/header.php'; 
        ?>

        <div class="p-6 lg:p-10 max-w-6xl animate-fade-in">
            <form action="" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <!-- Emails -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-200/60 dark:border-slate-800/50">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Configuration Emails</h3>
                    <p class="text-xs text-slate-500 font-medium mb-6">Identité de l'expéditeur pour les envois automatiques.</p>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom affiché</label>
                            <input type="text" name="mail_from_name" required value="<?php echo e($current_mail_name); ?>"
                                class="w-full px-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-sm">
                        </div>
                    </div>
                </div>

                <!-- Adhésions -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-200/60 dark:border-slate-800/50">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Période d'Adhésion</h3>
                    <p class="text-xs text-slate-500 font-medium mb-6">Contrôlez l'ouverture du portail public.</p>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Inscriptions Actives</span>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Visibilité du bouton d'adhésion</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="membership_enabled" value="0">
                                <label class="relative inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="membership_enabled" value="1" class="sr-only peer" <?php echo $membership_enabled ? 'checked' : ''; ?>>
                                    <div class="w-16 h-8 bg-slate-300 dark:bg-slate-700 rounded-full peer peer-checked:bg-emerald-500 transition-all relative shadow-inner">
                                        <!-- Icon Off (X) -->
                                        <div class="absolute left-1.5 top-1/2 -translate-y-1/2 text-white opacity-40 peer-checked:opacity-0 transition-opacity pointer-events-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l18 18"/></svg>
                                        </div>
                                        <!-- Icon On (Check) -->
                                        <div class="absolute right-1.5 top-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <!-- Knob -->
                                        <div class="absolute top-1 left-1 bg-white rounded-full h-6 w-6 transition-all peer-checked:translate-x-8 shadow-md"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-2">
                            <div class="w-2 h-2 rounded-full <?php echo $membership_enabled ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'; ?>"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest <?php echo $membership_enabled ? 'text-emerald-600' : 'text-slate-500'; ?>">
                                Status : <?php echo $membership_enabled ? 'Ouvert' : 'Fermé'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Base de données & Sauvegarde -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-200/60 dark:border-slate-800/50">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Sauvegarde Base de Données</h3>
                    <p class="text-xs text-slate-500 font-medium mb-6">Téléchargez l'intégralité des données de toutes les tables.</p>
                    
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Format de fichier d'export</label>
                            
                            <!-- Custom Radio Cards (Non-classic Select) -->
                            <div class="grid grid-cols-1 gap-3" id="format-cards-container">
                                <!-- Option 1: SQL -->
                                <label class="relative flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border-2 border-emerald-500 dark:border-emerald-500 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-800/80 transition-all group shadow-sm" id="label-format-sql">
                                    <input type="radio" name="export_format_option" value="sql" checked class="sr-only" onchange="selectExportFormat('sql')">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 font-black text-xs font-outfit">SQL</div>
                                        <div>
                                            <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Dump MySQL complet</span>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Structure & Données (.sql)</p>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-emerald-500 flex items-center justify-center bg-emerald-500 shrink-0" id="bullet-format-sql">
                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                    </div>
                                </label>

                                <!-- Option 2: JSON -->
                                <label class="relative flex items-center justify-between p-4 bg-white dark:bg-slate-800/20 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group" id="label-format-json">
                                    <input type="radio" name="export_format_option" value="json" class="sr-only" onchange="selectExportFormat('json')">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950 text-blue-600 rounded-xl flex items-center justify-center shrink-0 font-black text-xs font-outfit">JSON</div>
                                        <div>
                                            <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Données structurées</span>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Fichier textuel universel (.json)</p>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-700 flex items-center justify-center shrink-0" id="bullet-format-json">
                                        <div class="w-2 h-2 rounded-full bg-white hidden"></div>
                                    </div>
                                </label>

                                <!-- Option 3: CSV -->
                                <label class="relative flex items-center justify-between p-4 bg-white dark:bg-slate-800/20 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group" id="label-format-csv">
                                    <input type="radio" name="export_format_option" value="csv" class="sr-only" onchange="selectExportFormat('csv')">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-950 text-amber-600 rounded-xl flex items-center justify-center shrink-0 font-black text-xs font-outfit">CSV</div>
                                        <div>
                                            <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight">Tableurs Excel</span>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Un CSV par table compilé en (.zip)</p>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-700 flex items-center justify-center shrink-0" id="bullet-format-csv">
                                        <div class="w-2 h-2 rounded-full bg-white hidden"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Internal state storage -->
                        <input type="hidden" id="selected-export-format" value="sql">
                        
                        <button type="button" onclick="triggerDbExport()" class="w-full px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl transition-all shadow-lg shadow-amber-500/20 active:scale-95 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Lancer l'exportation
                        </button>
                    </div>
                </div>

                <!-- Importation Base de Données -->
                <div class="glass-panel p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-200/60 dark:border-slate-800/50">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2">Restauration Base de Données</h3>
                    <p class="text-xs text-slate-500 font-medium mb-6">Importez un fichier de sauvegarde (.sql, .json, ou .zip de CSV).</p>
                    
                    <div class="space-y-4">
                        <!-- Custom Premium File Picker -->
                        <div class="relative group cursor-pointer">
                            <input type="file" id="db-import-file" accept=".sql,.json,.zip" onchange="handleImportFileSelect()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="p-6 border-2 border-dashed border-slate-200 dark:border-slate-800 group-hover:border-emerald-500 rounded-2xl bg-slate-50/50 dark:bg-slate-900/10 text-center transition-all duration-300">
                                <svg class="w-8 h-8 text-slate-400 group-hover:text-emerald-500 mx-auto mb-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span id="import-file-label" class="text-xs font-black text-slate-600 dark:text-slate-400 group-hover:text-emerald-500 transition-colors uppercase tracking-tight block">Choisir un fichier</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Fichiers SQL, JSON ou ZIP acceptés</span>
                            </div>
                        </div>

                        <!-- Progress Bar (Hidden by default) -->
                        <div id="import-progress-container" class="hidden space-y-2 p-4 bg-slate-50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-800 rounded-2xl animate-pulse">
                            <div class="flex justify-between items-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <span id="import-progress-status">Importation en cours...</span>
                                <span id="import-progress-percent">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                <div id="import-progress-bar" class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-100" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button type="button" id="btn-import-submit" onclick="startDbImport()" disabled class="w-full px-6 py-4 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            Démarrer l'importation
                        </button>
                    </div>
                </div>

                <script>
                    function selectExportFormat(format) {
                        // Stocker le choix
                        document.getElementById('selected-export-format').value = format;
                        
                        // Réinitialiser les styles de tous les conteneurs
                        const formats = ['sql', 'json', 'csv'];
                        formats.forEach(f => {
                            const label = document.getElementById('label-format-' + f);
                            const bullet = document.getElementById('bullet-format-' + f);
                            
                            if (f === format) {
                                // Actif (Emerald style)
                                label.className = "relative flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border-2 border-emerald-500 dark:border-emerald-500 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-800/80 transition-all group shadow-sm";
                                bullet.className = "w-5 h-5 rounded-full border-2 border-emerald-500 flex items-center justify-center bg-emerald-500 shrink-0";
                                bullet.querySelector('div').classList.remove('hidden');
                            } else {
                                // Inactif (Classique)
                                label.className = "relative flex items-center justify-between p-4 bg-white dark:bg-slate-800/20 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group";
                                bullet.className = "w-5 h-5 rounded-full border border-slate-300 dark:border-slate-700 flex items-center justify-center shrink-0";
                                bullet.querySelector('div').classList.add('hidden');
                            }
                        });
                    }

                    function triggerDbExport() {
                        const format = document.getElementById('selected-export-format').value;
                        window.location.href = '<?php echo SITE_URL; ?>/admin/export_db.php?format=' + format;
                    }

                    // RESTAURATION / IMPORTATION JS FUNCTIONS
                    function handleImportFileSelect() {
                        const fileInput = document.getElementById('db-import-file');
                        const label = document.getElementById('import-file-label');
                        const btnSubmit = document.getElementById('btn-import-submit');
                        
                        if (fileInput.files.length > 0) {
                            const file = fileInput.files[0];
                            label.innerText = file.name;
                            label.className = "text-xs font-black text-emerald-600 dark:text-emerald-500 transition-colors uppercase tracking-tight block truncate";
                            
                            // Activer le bouton de soumission
                            btnSubmit.disabled = false;
                            btnSubmit.className = "w-full px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl transition-all shadow-lg hover:shadow-xl active:scale-95 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 cursor-pointer";
                        } else {
                            label.innerText = "Choisir un fichier";
                            label.className = "text-xs font-black text-slate-600 dark:text-slate-400 transition-colors uppercase tracking-tight block";
                            btnSubmit.disabled = true;
                            btnSubmit.className = "w-full px-6 py-4 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 cursor-not-allowed";
                        }
                    }

                    function startDbImport() {
                        const fileInput = document.getElementById('db-import-file');
                        if (fileInput.files.length === 0) return;
                        
                        if (!confirm("⚠️ ATTENTION : L'importation d'une sauvegarde va vider et remplacer TOUTES les données de votre base de données actuelle. Cette action est irréversible. Voulez-vous continuer ?")) {
                            return;
                        }
                        
                        const file = fileInput.files[0];
                        const formData = new FormData();
                        formData.append('backup_file', file);
                        
                        const xhr = new XMLHttpRequest();
                        const progressContainer = document.getElementById('import-progress-container');
                        const progressBar = document.getElementById('import-progress-bar');
                        const progressPercent = document.getElementById('import-progress-percent');
                        const progressStatus = document.getElementById('import-progress-status');
                        const btnSubmit = document.getElementById('btn-import-submit');
                        
                        // Désactiver les contrôles
                        btnSubmit.disabled = true;
                        btnSubmit.innerText = "Traitement...";
                        btnSubmit.className = "w-full px-6 py-4 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 cursor-not-allowed";
                        fileInput.disabled = true;
                        
                        // Afficher la barre de progression
                        progressContainer.classList.remove('hidden');
                        progressBar.style.width = '0%';
                        progressPercent.innerText = '0%';
                        progressStatus.innerText = "Téléversement du fichier...";
                        
                        // Progression du téléversement
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.style.width = percent + '%';
                                progressPercent.innerText = percent + '%';
                                
                                if (percent === 100) {
                                    progressStatus.innerText = "Analyse & Restauration (Veuillez patienter)...";
                                    progressContainer.classList.add('animate-pulse');
                                }
                            }
                        });
                        
                        // Fin de la requête
                        xhr.onload = function() {
                            progressContainer.classList.add('hidden');
                            btnSubmit.innerText = "Démarrer l'importation";
                            fileInput.disabled = false;
                            fileInput.value = ''; // Reset input
                            handleImportFileSelect();
                            
                            let response;
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch(e) {
                                response = { success: false, message: "Une erreur inattendue est survenue côté serveur." };
                            }
                            
                            if (xhr.status === 200 && response.success) {
                                if (typeof showToast === 'function') {
                                    showToast(response.message, 'success');
                                } else {
                                    alert(response.message);
                                }
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                if (typeof showToast === 'function') {
                                    showToast(response.message || "Erreur de restauration.", 'error');
                                } else {
                                    alert(response.message || "Erreur de restauration.");
                                }
                            }
                        };
                        
                        xhr.onerror = function() {
                            progressContainer.classList.add('hidden');
                            btnSubmit.disabled = false;
                            btnSubmit.innerText = "Démarrer l'importation";
                            fileInput.disabled = false;
                            handleImportFileSelect();
                            
                            if (typeof showToast === 'function') {
                                showToast("Erreur de connexion avec le serveur lors de l'importation.", 'error');
                            } else {
                                alert("Erreur de connexion avec le serveur lors de l'importation.");
                            }
                        };
                        
                        xhr.open('POST', '<?php echo SITE_URL; ?>/admin/import_db.php', true);
                        xhr.send(formData);
                    }
                </script>

                <!-- Submit -->
                <div class="lg:col-span-2">
                    <button type="submit" class="w-full lg:w-auto px-10 py-5 bg-slate-900 dark:bg-emerald-600 text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-xl active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer les Paramètres
                    </button>
                </div>
            </form>

            <!-- Newsletter Link -->
            <div class="mt-12 glass-panel p-8 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-800/50 shadow-lg shadow-slate-200/20">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Expédition Newsletter</h4>
                        <p class="text-[11px] text-slate-500 font-medium">Lien direct pour l'envoi des messages.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input type="text" readonly value="<?php echo SITE_URL; ?>/admin/newsletter/envoyer" class="flex-1 md:w-64 px-4 py-3 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 font-bold text-[10px] focus:outline-none">
                        <a href="<?php echo SITE_URL; ?>/admin/newsletter/envoyer" target="_blank" class="px-6 py-3 bg-amber-500 text-white font-black rounded-xl hover:bg-amber-600 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-amber-500/20">Ouvrir</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        <?php if ($message): ?>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof showToast === 'function') {
                    showToast("<?php echo addslashes($message); ?>", "<?php echo $status; ?>");
                } else {
                    alert("<?php echo addslashes($message); ?>");
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
