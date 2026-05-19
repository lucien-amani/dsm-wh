<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$news = null;
$error = '';

$id_param = isset($_GET['id']) ? $_GET['id'] : 0;
$id = 0;

if ($id_param) {
    if (is_numeric($id_param)) {
        $id = (int)$id_param;
    } else {
        $decoded = $hashids->decode($id_param);
        $id = !empty($decoded) ? $decoded[0] : 0;
    }
}

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $news = $stmt->fetch();
}

$is_social_post = (isset($news) && !empty($news['embed_code']) && empty($news['content']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $category = $_POST['category'] ?? '';
    $embed_code = $_POST['embed_code'] ?? '';
    $published = isset($_POST['published']) ? 1 : 0;

    if (empty($title) && !empty($embed_code)) {
        $title = "Post Social du " . date('d/m/Y');
    }

    if ($title && ($content || $embed_code)) {
        $slug = generateSlug($title);
        $image_name = $news ? $news['image'] : null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/';
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid() . '.' . $extension;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                $image_name = $new_name;
            }
        }

        if ($id) {
            $stmt = $pdo->prepare("UPDATE news SET title = :title, slug = :slug, content = :content, excerpt = :excerpt, category = :category, embed_code = :embed_code, image = :image, published = :published, author_id = :author_id WHERE id = :id");
            $stmt->execute([
                ':title' => $title, ':slug' => $slug, ':content' => $content, ':excerpt' => $excerpt,
                ':category' => $category, ':embed_code' => $embed_code, ':image' => $image_name,
                ':published' => $published, ':author_id' => $_SESSION['admin_id'], ':id' => $id
            ]);
            logAdminAction($_SESSION['admin_id'], "modification actualité : $title");
            header('Location: ' . SITE_URL . '/admin/actualites?msg=updated');
            exit;
        } else {
            // NanoID logic
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            do {
                $nanoid = '';
                for ($__i = 0; $__i < 10; $__i++) { $nanoid .= $chars[random_int(0, 61)]; }
                $chk = $pdo->prepare("SELECT 1 FROM news WHERE nanoid = ?");
                $chk->execute([$nanoid]);
            } while ($chk->fetch());

            $stmt = $pdo->prepare("INSERT INTO news (nanoid, title, slug, content, excerpt, category, embed_code, image, published, author_id) VALUES (:nanoid, :title, :slug, :content, :excerpt, :category, :embed_code, :image, :published, :author_id)");
            $stmt->execute([
                ':nanoid' => $nanoid, ':title' => $title, ':slug' => $slug, ':content' => $content, ':excerpt' => $excerpt,
                ':category' => $category, ':embed_code' => $embed_code, ':image' => $image_name,
                ':published' => $published, ':author_id' => $_SESSION['admin_id']
            ]);
            logAdminAction($_SESSION['admin_id'], "création actualité : $title");
            header('Location: ' . SITE_URL . '/admin/actualites?msg=created');
            exit;
        }
    } else {
        $error = 'Oups ! Le titre et le contenu sont indispensables (ou un lien de média).';
    }
}

$current_page = 'news';
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Modifier' : 'Rédiger'; ?> - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/dc2957p0surukua107br2308kw9cdrkzh90lbze1gvfyn0lj/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .tinymce-container .tox-tinymce {
            border-radius: 1.5rem !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <!-- Sticky Sub-Header for Controls -->
        <form method="POST" enctype="multipart/form-data">
        <header class="h-20 lg:h-24 sticky top-0 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-8 transition-all">
            <!-- Jeton CSRF pour la protection contre les attaques cross-site -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <div class="flex items-center gap-4">
                <a href="<?php echo SITE_URL; ?>/admin/actualites" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-lg lg:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter truncate max-w-[150px] lg:max-w-none"><?php echo $id ? 'Modifier' : 'Nouvel'; ?> <span class="text-emerald-600">Article</span></h1>
                <div id="autosave-status" class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-4 opacity-0 transition-opacity">
                    Sauvegarde...
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut:</span>
                    <label class="relative inline-flex items-center cursor-pointer scale-90">
                        <input type="checkbox" name="published" value="1" class="sr-only peer" <?php echo (!isset($news) || $news['published']) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-2 text-[10px] font-black text-slate-500 uppercase tracking-widest peer-checked:text-emerald-600">Public</span>
                    </label>
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 lg:px-8 lg:py-4 rounded-xl lg:rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-600/20 hover:-translate-y-1 transition-all flex items-center gap-2">
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
                <!-- Left Column: Content Editor -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-8">
                        <!-- Main Title Input -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Titre de l'Actualité</label>
                            <input type="text" name="title" value="<?php echo e($news['title'] ?? ''); ?>" placeholder="Entrez un titre percutant..."
                                   class="block w-full px-8 py-6 bg-slate-50 dark:bg-slate-800 border-none rounded-[2rem] focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-black text-3xl placeholder-slate-300">
                        </div>

                        <!-- TinyMCE Container -->
                        <div class="space-y-3 tinymce-container <?php echo $is_social_post ? 'hidden' : ''; ?>">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Corps de l'article</label>
                            <textarea id="content" name="content"><?php echo htmlspecialchars($news['content'] ?? ''); ?></textarea>
                        </div>

                        <!-- Embed Code Input -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2"><?php echo $is_social_post ? 'Lien ou Code d\'intégration' : 'Intégration Vidéo/Post (X, YouTube, LinkedIn...)'; ?></label>
                            <textarea name="embed_code" rows="5" placeholder="<?php echo $is_social_post ? 'Collez ici le lien du post ou le code d\'intégration complet...' : 'Collez l\'URL ou le code iFrame ici...'; ?>"
                                      class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-medium text-sm"><?php echo htmlspecialchars($news['embed_code'] ?? ''); ?></textarea>
                            <p class="text-[10px] font-medium text-slate-400 ml-2"><?php echo $is_social_post ? 'Collez simplement le lien (URL) du post ou cliquez sur "Embed" sur la plateforme et copiez le code ici.' : 'Copiez-collez ici le lien de la vidéo (ex: https://youtube.com/watch?v=...) ou le code d\'intégration complet.'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Media -->
                <div class="space-y-8">
                    <!-- Image Widget -->
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Image de Couverture</label>
                        <div class="group relative aspect-video bg-slate-50 dark:bg-slate-800 rounded-3xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center p-4">
                            <?php if($news && $news['image'] && file_exists('../uploads/' . $news['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/<?php echo $news['image']; ?>" id="preview-img" class="absolute inset-0 w-full h-full object-cover">
                            <?php else: ?>
                                <img id="preview-img" class="absolute inset-0 w-full h-full object-cover hidden" src="">
                            <?php endif; ?>
                            <div id="placeholder-ui" class="<?php echo ($news && $news['image']) ? 'hidden' : ''; ?> flex flex-col items-center">
                                <svg class="w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Glissez une image ici</p>
                            </div>
                            <input type="file" name="image" onchange="previewFile(this)" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                        </div>
                    </div>

                    <!-- Meta Data Widget -->
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Catégorie</label>
                            <input type="text" name="category" value="<?php echo e($news['category'] ?? ''); ?>" placeholder="ex: Politique, Social..."
                                   class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Brève Description (Extrait)</label>
                            <textarea name="excerpt" rows="4" placeholder="Un court résumé..."
                                      class="block w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-medium text-sm leading-relaxed"><?php echo htmlspecialchars($news['excerpt'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </main>

    <script>
        // Preview de l'image
        function previewFile(input) {
            const preview = document.getElementById('preview-img');
            const placeholder = document.getElementById('placeholder-ui');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // TinyMCE avec Switcher Dark/Light intelligent
            const isDark = document.documentElement.classList.contains('dark');
            console.log("TinyMCE detection:", typeof tinymce);
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#content',
                    height: 600,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace verticalbreak code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat | help',
                    content_style: 'body { font-family: Outfit, sans-serif; font-size:16px; padding: 2rem; }',
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

        // --- SAUVEGARDE AUTOMATIQUE (AUTOSAVE) ---
        let lastAutosaveData = "";
        
        function autosave() {
            const currentId = document.getElementById('item_id').value;
            const titleInput = document.querySelector('input[name="title"]');
            const excerptInput = document.querySelector('textarea[name="excerpt"]');
            const categoryInput = document.querySelector('input[name="category"]');
            const embedInput = document.querySelector('textarea[name="embed_code"]');
            
            if (!titleInput) return;

            const title = titleInput.value;
            const content = (typeof tinymce !== 'undefined' && tinymce.activeEditor) ? tinymce.activeEditor.getContent() : "";
            const excerpt = excerptInput ? excerptInput.value : "";
            const category = categoryInput ? categoryInput.value : "";
            const embed_code = embedInput ? embedInput.value : "";
            
            // Ne pas sauvegarder si tout est vide
            if (!title && !content && !excerpt) return;
            
            // Vérifier si les données ont changé pour éviter les requêtes inutiles
            const currentDataStr = JSON.stringify({title, content, excerpt, category, embed_code});
            if (currentDataStr === lastAutosaveData) return;
            
            const statusEl = document.getElementById('autosave-status');
            statusEl.innerText = "Sauvegarde...";
            statusEl.classList.remove('opacity-0');
            
            const formData = new FormData();
            formData.append('csrf_token', '<?php echo $_SESSION['csrf_token'] ?? ''; ?>');
            formData.append('type', 'news');
            formData.append('id', currentId);
            formData.append('data[title]', title);
            formData.append('data[content]', content);
            formData.append('data[excerpt]', excerpt);
            formData.append('data[category]', category);
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
