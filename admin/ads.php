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

// Action: Suppression
if (isset($_GET['delete'])) {
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    $id = (int)$_GET['delete'];
    
    // Récupérer l'image pour la supprimer du serveur
    $stmt = $pdo->prepare("SELECT image FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    
    if ($image && file_exists('../uploads/ads/' . $image)) {
        unlink('../uploads/ads/' . $image);
    }
    
    $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    
    logAdminAction($_SESSION['admin_id'], "suppression publicité ID $id");
    header('Location: ' . SITE_URL . '/admin/ads?msg=deleted');
    exit;
}

// Action: Toggle Statut
if (isset($_GET['toggle'])) {
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE ads SET is_active = 1 - is_active WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . SITE_URL . '/admin/ads?msg=updated');
    exit;
}

// Action: Ajouter / Modifier
$edit_ad = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    $edit_ad = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ad'])) {
    if (!validateCsrfToken($_POST['csrf'] ?? '')) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $title = trim($_POST['title']);
    $link = trim($_POST['link']);
    $slot = $_POST['slot'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
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

$ads_list = $pdo->query("SELECT * FROM ads ORDER BY created_at DESC")->fetchAll();
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Publicités - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Flatpickr for Modern Date Selection -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    
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
        $header_title = 'Gestion des <span class="text-amber-600">Publicités</span>';
        $header_subtitle = 'Monétisation & Bannières';
        $header_actions = '
            <a href="'.SITE_URL.'/admin/ads-edit" class="bg-amber-600 hover:bg-amber-700 text-white p-2.5 lg:px-8 lg:py-4 rounded-xl lg:rounded-[1.5rem] font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg shadow-amber-600/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Nouvelle Publicité</span>
            </a>';
        include 'includes/header.php'; 
        ?>

        <div class="p-8 space-y-8 animate-fade-in">
            <?php if (!empty($errors)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-600 p-6 rounded-3xl text-sm font-bold animate-shake">
                    <ul class="list-disc ml-5">
                        <?php foreach($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="glass-panel rounded-[3rem] shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Bannière</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Détails</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Stats</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Période</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <?php foreach($ads_list as $ad): ?>
                        <tr class="hover:bg-amber-50/10 dark:hover:bg-amber-900/5 transition-all group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="w-24 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-500 border border-slate-200 dark:border-slate-700">
                                    <img src="<?php echo SITE_URL; ?>/uploads/ads/<?php echo $ad['image']; ?>" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    <span class="font-extrabold text-slate-900 dark:text-white line-clamp-1 group-hover:text-amber-600 transition-colors"><?php echo e($ad['title']); ?></span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-md"><?php echo $ads_slots[$ad['slot']] ?? 'Inconnu'; ?></span>
                                        <a href="<?php echo e($ad['link']); ?>" target="_blank" class="text-[9px] font-bold text-slate-400 truncate max-w-[150px] hover:text-blue-500 underline"><?php echo e($ad['link']); ?></a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black text-slate-900 dark:text-white"><?php echo $ad['views']; ?></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Vues</span>
                                    </div>
                                    <div class="flex flex-col items-center border-l border-slate-100 dark:border-slate-800 pl-4">
                                        <span class="text-sm font-black text-slate-900 dark:text-white"><?php echo $ad['clicks']; ?></span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Clics</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex flex-col text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                    <span>Du <?php echo date('d/m/Y', strtotime($ad['start_date'])); ?></span>
                                    <span>Au <?php echo date('d/m/Y', strtotime($ad['end_date'])); ?></span>
                                    <?php 
                                        $now = date('Y-m-d H:i:s');
                                        $status = ($ad['is_active'] && $now >= $ad['start_date'] && $now <= $ad['end_date']) ? 'active' : 'inactive';
                                    ?>
                                    <span class="mt-1 <?php echo $status === 'active' ? 'text-emerald-500' : 'text-rose-500'; ?> uppercase text-[8px] font-black tracking-widest">
                                        ● <?php echo $status === 'active' ? 'En cours' : 'Arrêté'; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 lg:translate-x-4 lg:opacity-0 lg:group-hover:translate-x-0 lg:group-hover:opacity-100 transition-all duration-300">
                                    <a href="?toggle=<?php echo $ad['id']; ?>&csrf=<?php echo $csrf_token; ?>" class="w-11 h-11 <?php echo $ad['is_active'] ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'; ?> rounded-2xl flex items-center justify-center hover:shadow-lg transition-all border border-slate-200" title="Activer/Désactiver">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/admin/ads-edit?id=<?php echo $ad['id']; ?>" class="w-11 h-11 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-lg border border-blue-500/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <a href="?delete=<?php echo $ad['id']; ?>&csrf=<?php echo $csrf_token; ?>" onclick="return confirm('Supprimer définitivement cette publicité ?');" class="w-11 h-11 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg border border-rose-500/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($ads_list)): ?>
                            <tr><td colspan="5" class="p-20 text-center text-slate-400 italic font-bold">Aucune publicité enregistrée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Form modal removed -->

    <script>
        // JS logic removed as form is now on ads-edit.php
    </script>
</body>
</html>

