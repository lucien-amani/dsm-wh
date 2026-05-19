<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$current_page = 'ads';

// Liste des emplacements (Slots)
$ads_slots = [
    'news_list_top' => 'Liste Actus : Top (Slot A)',
    'news_list_infeed' => 'Liste Actus : In-feed (Slot B)',
    'news_list_bottom' => 'Liste Actus : Bas (Slot C)',
    'news_detail_top' => 'Détail Actu : Haut (Slot D)',
    'news_detail_content' => 'Détail Actu : Milieu Article (Slot E)',
    'news_detail_sidebar' => 'Détail Actu : Sidebar (Slot F)',
    'news_detail_bottom' => 'Détail Actu : Avant Commentaires (Slot G)'
];

$edit_ad = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    $edit_ad = $stmt->fetch();
    
    if (!$edit_ad) {
        header('Location: ' . SITE_URL . '/admin/ads');
        exit;
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ad'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $title = trim($_POST['title']);
    $link = trim($_POST['link']);
    $slot = $_POST['slot'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($title)) $errors[] = "Le titre est obligatoire.";
    if (empty($link)) $errors[] = "Le lien est obligatoire.";
    if (!isset($ads_slots[$slot])) $errors[] = "Emplacement invalide.";
    
    $image_name = $edit_ad['image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/ads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid('ad_') . '.' . $ext;
        
        if (compressImage($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
            if ($image_name && file_exists($upload_dir . $image_name)) {
                unlink($upload_dir . $image_name);
            }
            $image_name = $new_name;
        } else {
            $errors[] = "Erreur lors de l'upload de l'image.";
        }
    } elseif (!$id && empty($image_name)) {
        $errors[] = "L'image est obligatoire pour une nouvelle publicité.";
    }
    
    if (empty($errors)) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE ads SET title = ?, image = ?, link = ?, slot = ?, start_date = ?, end_date = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $image_name, $link, $slot, $start_date, $end_date, $is_active, $id]);
            logAdminAction($_SESSION['admin_id'], "modification publicité : $title");
        } else {
            $stmt = $pdo->prepare("INSERT INTO ads (title, image, link, slot, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $image_name, $link, $slot, $start_date, $end_date, $is_active]);
            logAdminAction($_SESSION['admin_id'], "ajout publicité : $title");
        }
        header('Location: ' . SITE_URL . '/admin/ads?msg=success');
        exit;
    }
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_ad ? 'Modifier' : 'Nouvelle'; ?> Publicité - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css?v=1.0.2">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth relative">
        <!-- Background Blobs -->
        <div class="absolute top-0 right-0 -z-10 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute bottom-0 left-0 -z-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>

        <!-- Header -->
        <header class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50 transition-all">
            <div class="flex items-center gap-4">
                <a href="<?php echo SITE_URL; ?>/admin/ads" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-amber-600 hover:text-white transition-all shadow-sm group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl lg:text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate">
                        <?php echo $edit_ad ? 'Modifier' : 'Configurer'; ?> <span class="text-amber-600">Publicité</span>
                    </h1>
                </div>
            </div>
            
            <div class="hidden md:flex items-center gap-3">
                <div class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Édition en cours</span>
                </div>
            </div>
        </header>

        <div class="p-4 lg:p-12 max-w-7xl mx-auto w-full animate-fade-in">
            <?php if (!empty($errors)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-600 p-6 rounded-3xl text-sm font-bold mb-8 animate-shake flex items-start gap-4">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <ul class="list-disc ml-5">
                        <?php foreach($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="id" value="<?php echo $edit_ad['id'] ?? ''; ?>">
                <input type="hidden" name="slot" id="form-slot-hidden" value="<?php echo $edit_ad['slot'] ?? 'news_list_top'; ?>">
                <input type="hidden" name="save_ad" value="1">

                <!-- Left Column: Form Controls -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Basic Information Card -->
                    <div class="glass-panel p-8 lg:p-10 rounded-[3rem] shadow-2xl bg-white/60 dark:bg-slate-900/60 border border-white/20 dark:border-slate-800 backdrop-blur-xl">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Informations <span class="text-amber-600">Générales</span></h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Configurez l'identité et le lien de votre campagne</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Titre de la campagne</label>
                                    <div class="relative group">
                                        <input type="text" name="title" id="ad-title-input" value="<?php echo e($edit_ad['title'] ?? ''); ?>" placeholder="Ex: Pack Promo Été 2024" class="premium-input pl-14" oninput="updatePreview()">
                                        <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">URL de destination</label>
                                    <div class="relative group">
                                        <input type="url" name="link" value="<?php echo e($edit_ad['link'] ?? ''); ?>" placeholder="https://votre-site.com/promo" class="premium-input pl-14">
                                        <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 105.656 5.656l1.1 1.1"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-slate-50 dark:bg-slate-800/40 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Activer la diffusion</p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tight">Rendre la publicité visible immédiatement</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" class="sr-only peer" <?php echo (!isset($edit_ad) || $edit_ad['is_active']) ? 'checked' : ''; ?>>
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[1.25rem] after:w-[1.25rem] after:transition-all dark:border-gray-600 peer-checked:bg-amber-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Slot Selection Card -->
                    <div class="glass-panel p-8 lg:p-10 rounded-[3rem] shadow-2xl bg-white/60 dark:bg-slate-900/60 border border-white/20 dark:border-slate-800 backdrop-blur-xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Emplacement <span class="text-indigo-600">Stratégique</span></h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Où apparaîtra votre publicité ?</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach($ads_slots as $val => $lbl): ?>
                                <?php 
                                    $parts = explode(':', $lbl);
                                    $category = trim($parts[0]);
                                    $position = trim($parts[1] ?? $lbl);
                                ?>
                                <div class="slot-card group <?php echo (isset($edit_ad['slot']) && $edit_ad['slot'] === $val) ? 'selected' : ''; ?>" 
                                     onclick="selectSlot('<?php echo $val; ?>', this)">
                                    <div class="check-icon">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <div class="slot-icon w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-amber-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[8px] font-black text-amber-600 uppercase tracking-widest mb-0.5"><?php echo $category; ?></span>
                                            <span class="text-xs font-extrabold text-slate-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-500 transition-colors"><?php echo $position; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Scheduling Card -->
                    <div class="glass-panel p-8 lg:p-10 rounded-[3rem] shadow-2xl bg-white/60 dark:bg-slate-900/60 border border-white/20 dark:border-slate-800 backdrop-blur-xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Planification <span class="text-emerald-600">Temporelle</span></h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Définissez la période de validité</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Date de début</span>
                                <div class="relative group">
                                    <input type="text" name="start_date" id="form-start" value="<?php echo isset($edit_ad['start_date']) ? date('Y-m-d H:i', strtotime($edit_ad['start_date'])) : date('Y-m-d H:i'); ?>" class="premium-input pl-14 cursor-pointer">
                                    <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Date d'expiration</span>
                                <div class="relative group">
                                    <input type="text" name="end_date" id="form-end" value="<?php echo isset($edit_ad['end_date']) ? date('Y-m-d H:i', strtotime($edit_ad['end_date'])) : date('Y-m-d H:i', strtotime('+1 month')); ?>" class="premium-input pl-14 cursor-pointer">
                                    <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Media & Actions -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Media Upload Card -->
                    <div class="glass-panel p-8 rounded-[3rem] shadow-2xl bg-white/60 dark:bg-slate-900/60 border border-white/20 dark:border-slate-800 backdrop-blur-xl">
                        <div class="flex flex-col items-center text-center mb-8">
                            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-3xl flex items-center justify-center text-amber-600 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">Visuel <span class="text-amber-600">Média</span></h3>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Format recommandé : 1200x300px</p>
                        </div>

                        <div id="upload-container" class="upload-area group <?php echo isset($edit_ad['image']) ? 'has-image' : ''; ?> aspect-[4/3] flex items-center justify-center" onclick="document.getElementById('image-input').click()">
                            <input type="file" name="image" id="image-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                            
                            <div id="upload-placeholder" class="<?php echo isset($edit_ad['image']) ? 'hidden' : ''; ?> transition-all group-hover:scale-110">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-400 group-hover:text-amber-600 transition-colors">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-widest">Choisir un fichier</p>
                            </div>

                            <div id="preview-container" class="absolute inset-0 <?php echo isset($edit_ad['image']) ? '' : 'hidden'; ?>">
                                <img id="image-preview" src="<?php echo isset($edit_ad['image']) ? SITE_URL . '/uploads/ads/' . $edit_ad['image'] : ''; ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-2 backdrop-blur-sm">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </div>
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest">Changer l'image</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Card -->
                    <div class="glass-panel p-8 rounded-[3rem] shadow-2xl bg-slate-950 border border-white/10 overflow-hidden relative group">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-600/20 rounded-full blur-2xl -mr-12 -mt-12 group-hover:bg-amber-600/40 transition-colors"></div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-[10px] font-black text-white uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Aperçu en direct
                            </span>
                            <span class="text-[8px] font-bold text-slate-500 uppercase">Mode Simulation</span>
                        </div>

                        <div class="space-y-4">
                            <div class="w-full aspect-[3/1] bg-slate-900 rounded-xl overflow-hidden border border-white/5 relative">
                                <img id="preview-mockup-img" src="<?php echo isset($edit_ad['image']) ? SITE_URL . '/uploads/ads/' . $edit_ad['image'] : 'https://placehold.co/1200x300/1e293b/475569?text=Votre+Publicit%C3%A9'; ?>" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 px-2 py-0.5 bg-black/50 backdrop-blur-md rounded text-[7px] text-white/70 font-black uppercase tracking-widest">Sponsorisé</div>
                            </div>
                            <div class="space-y-1">
                                <h4 id="preview-mockup-title" class="text-sm font-black text-white truncate"><?php echo $edit_ad['title'] ?? 'Titre de la campagne'; ?></h4>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                                    <div class="h-1.5 w-24 bg-slate-800 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white px-8 py-7 rounded-[2.5rem] font-black uppercase tracking-[0.3em] text-[10px] shadow-2xl shadow-amber-600/30 hover:-translate-y-2 active:scale-95 transition-all flex items-center justify-center gap-4 group">
                            <span><?php echo $edit_ad ? 'Mettre à jour' : 'Enregistrer'; ?> la campagne</span>
                            <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        
                        <p class="text-center text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-6">
                            L'enregistrement créera une entrée dans les journaux système
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Initialisation de Flatpickr
        flatpickr("#form-start", { 
            enableTime: true, 
            dateFormat: "Y-m-d H:i", 
            locale: "fr", 
            disableMobile: "true",
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('premium-flatpickr');
            }
        });
        flatpickr("#form-end", { 
            enableTime: true, 
            dateFormat: "Y-m-d H:i", 
            locale: "fr", 
            disableMobile: "true",
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('premium-flatpickr');
            }
        });

        const slotIcons = {
            'news_list_top': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>',
            'news_list_infeed': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>',
            'news_list_bottom': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/></svg>',
            'news_detail_top': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>',
            'news_detail_content': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'news_detail_sidebar': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'news_detail_bottom': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>'
        };

        function selectSlot(value, element) {
            document.querySelectorAll('.slot-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('form-slot-hidden').value = value;
            updateMockupDisplay(value);
        }

        function updateMockupDisplay(slot) {
            // Optionnel : on pourrait changer l'aspect visuel du mockup ici
            console.log("Slot sélectionné :", slot);
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('preview-mockup-img').src = e.target.result;
                    document.getElementById('preview-container').classList.remove('hidden');
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('upload-container').classList.add('has-image');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updatePreview() {
            const titleInput = document.getElementById('ad-title-input');
            const mockupTitle = document.getElementById('preview-mockup-title');
            mockupTitle.innerText = titleInput.value || 'Titre de la campagne';
        }

        // Keyboard Shortcut Ctrl+S
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
        });

        // Initialize Icons
        document.querySelectorAll('.slot-card').forEach(card => {
            const slot = card.getAttribute('onclick').match(/'([^']+)'/)[1];
            const iconContainer = card.querySelector('.slot-icon');
            if (iconContainer && slotIcons[slot]) {
                iconContainer.innerHTML = slotIcons[slot];
            }
        });
    </script>
</body>
</html>
