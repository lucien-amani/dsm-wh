<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$project = null;
$error = '';

// Récupérer les catégories existantes
$stmt_cats = $pdo->query("SELECT name FROM project_categories ORDER BY name ASC");
$existing_categories = $stmt_cats->fetchAll(PDO::FETCH_COLUMN);

$id_param = isset($_GET['id']) ? $_GET['id'] : 0;
$id = 0;

if ($id_param) {
    if (is_numeric($id_param)) {
        $id = (int)$id_param;
    } else {
        // Tenter de décoder le hash
        $decoded = $hashids->decode($id_param);
        $id = !empty($decoded) ? $decoded[0] : 0;
    }
}

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $project = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $location = $_POST['location'] ?? '';
    $status = $_POST['status'] ?? 'En cours';
    $description = $_POST['description'] ?? '';
    $content = $_POST['content'] ?? '';
    $embed_code = $_POST['embed_code'] ?? '';
    $beneficiaries = $_POST['beneficiaries'] ?? '';
    $published = isset($_POST['published']) ? 1 : 0;

    if (empty($title) && !empty($embed_code)) {
        $title = "Média : Projet du " . date('d/m/Y');
    }

    $slug = generateSlug($title);

    // Ensure slug is unique
    $original_slug = $slug;
    $counter = 1;
    while (true) {
        $stmt_check = $id ? $pdo->prepare("SELECT id FROM projects WHERE slug = ? AND id != ?") : $pdo->prepare("SELECT id FROM projects WHERE slug = ?");
        $id ? $stmt_check->execute([$slug, $id]) : $stmt_check->execute([$slug]);
        if ($stmt_check->rowCount() > 0) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        } else {
            break;
        }
    }

    // Sauvegarder la nouvelle catégorie si elle n'existe pas
    if ($category && !in_array($category, $existing_categories)) {
        $stmt_add_cat = $pdo->prepare("INSERT IGNORE INTO project_categories (name) VALUES (?)");
        $stmt_add_cat->execute([$category]);
    }

    $image_name = $project ? $project['image'] : null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/';
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
            $image_name = $new_name;
        }
    }

    if ($title && ($description || $embed_code)) {
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE projects SET title = :title, slug = :slug, category = :category, location = :location, status = :status, description = :description, content = :content, embed_code = :embed_code, beneficiaries = :beneficiaries, image = :image, published = :published, author_id = :author_id WHERE id = :id");
                $stmt->execute([
                    ':title' => $title, ':slug' => $slug, ':category' => $category, ':location' => $location,
                    ':status' => $status, ':description' => $description, ':content' => $content, ':embed_code' => $embed_code,
                    ':beneficiaries' => $beneficiaries, ':image' => $image_name, ':published' => $published, 
                    ':author_id' => $_SESSION['admin_id'], ':id' => $id
                ]);
                logAdminAction($_SESSION['admin_id'], "modification projet : $title");
                header('Location: ' . SITE_URL . '/admin/projets?msg=updated');
                exit;
            } else {
                    // Générer un NanoID unique pour ce projet
                    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    do {
                        $nanoid = '';
                        for ($__i = 0; $__i < 10; $__i++) { $nanoid .= $chars[random_int(0, 61)]; }
                        $chk = $pdo->prepare("SELECT 1 FROM projects WHERE nanoid = ?");
                        $chk->execute([$nanoid]);
                    } while ($chk->fetch());

                    $stmt = $pdo->prepare("INSERT INTO projects (nanoid, title, slug, category, location, status, description, content, embed_code, beneficiaries, image, published, author_id) VALUES (:nanoid, :title, :slug, :category, :location, :status, :description, :content, :embed_code, :beneficiaries, :image, :published, :author_id)");
                    $stmt->execute([
                        ':nanoid' => $nanoid, ':title' => $title, ':slug' => $slug, ':category' => $category, ':location' => $location,
                        ':status' => $status, ':description' => $description, ':content' => $content, ':embed_code' => $embed_code,
                        ':beneficiaries' => $beneficiaries, ':image' => $image_name, ':published' => $published,
                        ':author_id' => $_SESSION['admin_id']
                    ]);
                    logAdminAction($_SESSION['admin_id'], "création projet : $title");
                    header('Location: ' . SITE_URL . '/admin/projets?msg=created');
                    exit;
            }
        } catch (PDOException $e) {
            $error = "Erreur SQL : " . $e->getMessage();
        }
    } else {
        $error = 'Le titre et la description sont requis (ou un lien vidéo/média).';
    }
}

$current_page = 'projects';
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Modifier' : 'Nouveau'; ?> Projet - DSM ADMIN</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/dc2957p0surukua107br2308kw9cdrkzh90lbze1gvfyn0lj/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .tinymce-container .tox-tinymce {
            border-radius: 1.5rem !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <form method="POST" enctype="multipart/form-data">
        <header class="h-20 lg:h-24 sticky top-0 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-8 transition-all">
            <div class="flex items-center gap-4">
                <a href="<?php echo SITE_URL; ?>/admin/projets" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-lg lg:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate max-w-[150px] lg:max-w-none"><?php echo $id ? 'Modifier' : 'Nouveau'; ?> <span class="text-blue-600">Projet</span></h1>
                <div id="autosave-status" class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 opacity-0 transition-opacity">
                    Sauvegarde...
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Exposition:</span>
                    <label class="relative inline-flex items-center cursor-pointer scale-90">
                        <input type="checkbox" name="published" value="1" class="sr-only peer" <?php echo (!isset($project) || $project['published']) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-3 lg:px-8 lg:py-4 rounded-xl lg:rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-blue-600/20 hover:-translate-y-1 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span class="hidden sm:inline">Enregistrer</span>
                </button>
            </div>
        </header>

        <div class="p-8 pb-20 max-w-6xl mx-auto w-full animate-fade-in">
            <?php if($error): ?>
                <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-3xl text-sm font-bold animate-shake">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="id" id="item_id" value="<?php echo $id; ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Project Info -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nom du Projet</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($project['title'] ?? ''); ?>" placeholder="Titre du projet (optionnel si media)..."
                                   class="block w-full px-8 py-6 bg-slate-50 dark:bg-slate-800 border-none rounded-[2rem] focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-black text-3xl placeholder-slate-300">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Localisation</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <input type="text" name="location" value="<?php echo htmlspecialchars($project['location'] ?? ''); ?>" placeholder="ex: Bukavu"
                                           class="block w-full pl-12 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-bold text-sm">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">État d'avancement</label>
                                <div class="relative group">
                                    <div id="status-icon-container" class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors <?php 
                                        $status = $project['status'] ?? 'En cours';
                                        echo $status === 'Terminé' ? 'text-emerald-500' : ($status === 'Planifié' ? 'text-blue-500' : 'text-amber-500'); 
                                    ?>">
                                        <svg id="status-svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?php if(($project['status'] ?? '') === 'Terminé'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            <?php elseif(($project['status'] ?? '') === 'Planifié'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            <?php else: ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            <?php endif; ?>
                                        </svg>
                                    </div>
                                    <select name="status" id="status-select" class="block w-full pl-12 pr-10 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-bold text-sm appearance-none cursor-pointer">
                                        <option value="En cours" <?php echo ($project['status'] ?? '') === 'En cours' ? 'selected' : ''; ?>>En cours de réalisation</option>
                                        <option value="Terminé" <?php echo ($project['status'] ?? '') === 'Terminé' ? 'selected' : ''; ?>>Projet terminé & livré</option>
                                        <option value="Planifié" <?php echo ($project['status'] ?? '') === 'Planifié' ? 'selected' : ''; ?>>En phase de planification</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 tinymce-container">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Détails techniques</label>
                            <textarea id="content" name="content"><?php echo htmlspecialchars($project['content'] ?? ''); ?></textarea>
                        </div>

                        <!-- Embed Code Input -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Intégration Vidéo/Post (X, YouTube, LinkedIn...)</label>
                            <input type="text" name="embed_code" value="<?php echo htmlspecialchars($project['embed_code'] ?? ''); ?>" placeholder="Collez l'URL ou le code iFrame ici..."
                                   class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-medium text-sm">
                            <p class="text-[10px] font-medium text-slate-400 ml-2">Copiez-collez ici le lien de la vidéo ou le code d'intégration du réseau social associé à ce projet.</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Media & Specs -->
                <div class="space-y-8">
                    <!-- Image Widget -->
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Image Principale</label>
                        <div class="group relative aspect-video bg-slate-50 dark:bg-slate-800 rounded-3xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center p-4">
                            <?php 
                            $image_exists = ($project && $project['image'] && file_exists('../uploads/' . $project['image']));
                            if($project && $project['image'] && $image_exists): 
                            ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $project['image']; ?>" id="preview-img" class="absolute inset-0 w-full h-full object-cover">
                            <?php else: ?>
                                <img id="preview-img" class="absolute inset-0 w-full h-full object-cover hidden" src="">
                                <?php if($project && $project['image'] && !$image_exists): ?>
                                    <!-- Debug: File <?php echo $project['image']; ?> not found in ../uploads/ -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-rose-500/10 text-rose-500 p-4 text-center">
                                        <div class="text-[10px] font-black uppercase">Fichier introuvable</div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div id="placeholder-ui" class="<?php echo ($project && $project['image']) ? 'hidden' : ''; ?> flex flex-col items-center">
                                <svg class="w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Image du Projet</p>
                            </div>
                            <input type="file" name="image" onchange="previewFile(this)" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                        </div>
                    </div>

                    <!-- Meta Data Widget -->
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Secteur / Catégorie</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <input list="category-list" name="category" value="<?php echo htmlspecialchars($project['category'] ?? ''); ?>" placeholder="Saisir ou choisir..."
                                       class="block w-full pl-12 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-bold text-sm">
                                <datalist id="category-list">
                                    <?php foreach($existing_categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Impact / Bénéficiaires</label>
                            <input type="text" name="beneficiaries" value="<?php echo htmlspecialchars($project['beneficiaries'] ?? ''); ?>" placeholder="ex: 50 000 habitants"
                                   class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Description Courte</label>
                            <textarea name="description" rows="4" placeholder="Ce qui apparaîtra sur la carte du projet..."
                                      class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white font-medium text-sm leading-relaxed"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </main>

    <script>
        function previewFile(input) {
            const preview = document.getElementById('preview-img');
            const placeholder = document.getElementById('placeholder-ui');
            const file = input.files[0];
            const reader = new FileReader();
            reader.onloadend = function() { preview.src = reader.result; preview.classList.remove('hidden'); placeholder.classList.add('hidden'); }
            if (file) reader.readAsDataURL(file);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            console.log("TinyMCE detection:", typeof tinymce);
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#content',
                    height: 500,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace verticalbreak code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat | help',
                    content_style: 'body { font-family: Outfit, sans-serif; font-size:16px; padding: 1.5rem; }',
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    branding: false,
                    menubar: false,
                    statusbar: false,
                    setup: function(editor) {
                        editor.on('init', function() {
                            editor.getContainer().style.borderRadius = '1.5rem';
                        });
                    }
                });
            } else {
                console.error("TinyMCE failed to load.");
            }
        });

        // Dynamisme de l'icône de statut
        const statusSelect = document.getElementById('status-select');
        const statusIconContainer = document.getElementById('status-icon-container');
        const statusSvg = document.getElementById('status-svg');

        const statusMaps = {
            'En cours': {
                color: 'text-amber-500',
                path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
            },
            'Terminé': {
                color: 'text-emerald-500',
                path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            },
            'Planifié': {
                color: 'text-blue-500',
                path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            }
        };

        statusSelect.addEventListener('change', function() {
            const config = statusMaps[this.value];
            if (config) {
                // Reset colors
                statusIconContainer.classList.remove('text-amber-500', 'text-emerald-500', 'text-blue-500');
                // Set new color & path
                statusIconContainer.classList.add(config.color);
                statusSvg.innerHTML = config.path;
            }
        });

        // --- SAUVEGARDE AUTOMATIQUE (AUTOSAVE) ---
        let lastAutosaveData = "";
        
        function autosave() {
            const currentId = document.getElementById('item_id').value;
            const titleInput = document.querySelector('input[name="title"]');
            const descriptionInput = document.querySelector('textarea[name="description"]');
            const categoryInput = document.querySelector('input[name="category"]');
            const locationInput = document.querySelector('input[name="location"]');
            const statusInput = document.querySelector('select[name="status"]');
            const beneficiariesInput = document.querySelector('input[name="beneficiaries"]');
            const embedInput = document.querySelector('input[name="embed_code"]');
            
            if (!titleInput) return;

            const title = titleInput.value;
            const content = (typeof tinymce !== 'undefined' && tinymce.activeEditor) ? tinymce.activeEditor.getContent() : "";
            const description = descriptionInput ? descriptionInput.value : "";
            const category = categoryInput ? categoryInput.value : "";
            const location = locationInput ? locationInput.value : "";
            const status = statusInput ? statusInput.value : "";
            const beneficiaries = beneficiariesInput ? beneficiariesInput.value : "";
            const embed_code = embedInput ? embedInput.value : "";
            
            // Ne pas sauvegarder si tout est vide
            if (!title && !content && !description) return;
            
            // Vérifier si les données ont changé
            const currentDataStr = JSON.stringify({title, content, description, category, location, status, beneficiaries, embed_code});
            if (currentDataStr === lastAutosaveData) return;
            
            const statusEl = document.getElementById('autosave-status');
            statusEl.innerText = "Sauvegarde...";
            statusEl.classList.remove('opacity-0');
            
            const formData = new FormData();
            formData.append('type', 'project');
            formData.append('id', currentId);
            formData.append('data[title]', title);
            formData.append('data[content]', content);
            formData.append('data[description]', description);
            formData.append('data[category]', category);
            formData.append('data[location]', location);
            formData.append('data[status]', status);
            formData.append('data[beneficiaries]', beneficiaries);
            formData.append('data[embed_code]', embed_code);
            
            fetch('<?php echo SITE_URL; ?>/admin/api/autosave.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('item_id').value = result.id;
                    lastAutosaveData = currentDataStr;
                    statusEl.innerText = "Enregistré à " + result.time;
                    setTimeout(() => {
                        if (statusEl.innerText.includes(result.time)) statusEl.classList.add('opacity-0');
                    }, 3000);
                }
            })
            .catch(err => {
                console.error("Autosave error:", err);
                statusEl.innerText = "Erreur de synchro";
            });
        }
        
        // Lancer l'autosave toutes les 30 secondes
        setInterval(autosave, 30000);
        
        // Sauvegarder aussi quand on quitte le titre
        document.querySelector('input[name="title"]').addEventListener('blur', autosave);
    </script>
</body>
</html>
