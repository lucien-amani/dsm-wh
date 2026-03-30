<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/router.php';

require_once 'includes/auth.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Récupérer les données pour le formulaire
$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers WHERE status = 'active' ORDER BY email ASC")->fetchAll();
$news_list = $pdo->query("SELECT id, title, created_at FROM news WHERE published = 1 ORDER BY created_at DESC")->fetchAll();
$projects_list = $pdo->query("SELECT id, title, created_at FROM projects WHERE published = 1 ORDER BY created_at DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_news = $_POST['news'] ?? [];
    $selected_projects = $_POST['projects'] ?? [];
    $selected_subscribers = $_POST['subscribers'] ?? [];
    $subject = $_POST['subject'] ?? 'Actualités de Ir. Samy Magadju';

    if (empty($selected_subscribers)) {
        $error = "Veuillez sélectionner au moins un abonné.";
    } elseif (empty($selected_news) && empty($selected_projects)) {
        $error = "Veuillez sélectionner au moins un article ou un projet.";
    } else {
        // Préparer le contenu
        $content_blocks = [];

        // Fetch news details
        if (!empty($selected_news)) {
            $ids = implode(',', array_map('intval', $selected_news));
            $news_data = $pdo->query("SELECT * FROM news WHERE id IN ($ids)")->fetchAll();
            foreach ($news_data as $n) {
                $content_blocks[] = [
                    'type' => 'Actualité',
                    'title' => $n['title'],
                    'image' => $n['image'],
                    'excerpt' => $n['excerpt'],
                    'content' => $n['content'],
                    'url' => getUrl('news', $n['id'], $n['slug'])
                ];
            }
        }

        // Fetch project details
        if (!empty($selected_projects)) {
            $ids = implode(',', array_map('intval', $selected_projects));
            $projects_data = $pdo->query("SELECT * FROM projects WHERE id IN ($ids)")->fetchAll();
            foreach ($projects_data as $p) {
                $content_blocks[] = [
                    'type' => 'Projet',
                    'title' => $p['title'],
                    'image' => $p['image'],
                    'excerpt' => $p['description'], // synthesis
                    'content' => $p['content'],
                    'url' => getUrl('projects', $p['id'], $p['slug'])
                ];
            }
        }

        // Construction du corps de l'email (HTML Premium)
        $email_html = "
        <div style='font-family: \"Outfit\", sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;'>
            <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 24px; overflow: hidden; shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
                <!-- Header -->
                <div style='background: #0f172a; padding: 40px; text-align: center; border-bottom: 4px solid #f59e0b;'>
                    <img src='" . SITE_URL . "/assets/logo/logo-dsm.jpg' alt='Logo DSM' style='height: 80px; width: 80px; border-radius: 50%; border: 3px solid #f59e0b; margin-bottom: 15px;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -1px; text-transform: uppercase;'>La Dynamique Samy Magadju/Wema ni Hakiba ASBL</h1>
                    <p style='color: #94a3b8; font-size: 12px; font-weight: 700; margin-top: 5px; text-transform: uppercase; tracking: 0.2em;'>Newsletter Officielle</p>
                </div>

                <!-- Content -->
                <div style='padding: 40px;'>
                    <h2 style='font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 25px;'>Découvrez nos dernières actions</h2>
        ";

        foreach ($content_blocks as $block) {
            $intro = strip_tags($block['content']);
            $intro = mb_substr($intro, 0, 200) . '...';
            $img_url = $block['image'] ? SITE_URL . '/uploads/' . $block['image'] : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80';

            $email_html .= "
                <div style='margin-bottom: 40px; border-bottom: 1px solid #f1f5f9; pb: 40px;'>
                    <div style='height: 200px; width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 20px;'>
                        <img src='{$img_url}' style='width: 100%; height: 100%; object-fit: cover;' alt='{$block['title']}'>
                    </div>
                    <span style='display: inline-block; background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 900; text-transform: uppercase; margin-bottom: 10px;'>{$block['type']}</span>
                    <h3 style='font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;'>{$block['title']}</h3>
                    <p style='font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 12px;'>{$block['excerpt']}</p>
                    <p style='font-size: 13px; font-weight: 400; color: #94a3b8; line-height: 1.6; margin-bottom: 20px;'>{$intro}</p>
                    <a href='{$block['url']}' style='display: inline-block; background: #0f172a; color: #ffffff; padding: 12px 25px; border-radius: 12px; font-size: 12px; font-weight: 800; text-decoration: none; text-transform: uppercase;'>Lire la suite</a>
                </div>
            ";
        }

        $email_html .= "
                </div>

                <!-- Footer -->
                <div style='background: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #f1f5f9;'>
                    <p style='font-size: 11px; color: #94a3b8; line-height: 1.6;'>
                        Vous recevez cet email car vous êtes abonné à la newsletter de la Dynamique Samy Magadju/Wema ni Hakiba ASBL.<br>
                        &copy; " . date('Y') . " DSM - Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
        ";

        // Envoi aux abonnés
        $success_count = 0;
        $fail_count = 0;

        foreach ($selected_subscribers as $sub_id) {
            $stmt_sub = $pdo->prepare("SELECT email FROM newsletter_subscribers WHERE id = ?");
            $stmt_sub->execute([$sub_id]);
            $email_to = $stmt_sub->fetchColumn();

            if ($email_to) {
                // Ajouter le footer de désabonnement personnalisé pour chaque email
                $unsub_url = SITE_URL . "/api/unsubscribe.php?email=" . base64_encode($email_to);
                $personalized_html = $email_html . "
                    <div style='text-align: center; padding: 20px; font-family: sans-serif;'>
                         <a href='{$unsub_url}' style='font-size: 11px; color: #94a3b8; text-decoration: underline;'>Se désabonner de cette newsletter</a>
                    </div>
                ";
                
                if (sendMail($email_to, $subject, $personalized_html)) {
                    $success_count++;
                } else {
                    $fail_count++;
                }
            } else {
                $fail_count++;
            }
        }

        $message = "Newsletter envoyée avec succès à $success_count abonné(s).";
        if ($fail_count > 0) {
            $message .= " ($fail_count erreur(s)).";
        }
    }
}

$current_page = 'newsletter';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer une Newsletter - DSM ADMIN</title>
    <link rel="stylesheet" href="/dsm/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-panel { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); }
        
        input[type="checkbox"]:checked + label { border-color: #f59e0b; background-color: rgba(245, 158, 11, 0.05); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 overflow-hidden">
        <header class="h-24 sticky top-0 z-40 bg-white/70 dark:bg-slate-950/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 lg:px-12">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Envoyer <span class="text-amber-600">Newsletter</span></h1>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em] mt-1">Composer et diffuser aux abonnés</p>
            </div>
            <a href="/dsm/admin/newsletter" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
        </header>

        <div class="p-8 lg:p-12 overflow-y-auto">
            <?php if ($message): ?>
                <div class="mb-8 p-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-500/30 rounded-[2rem] text-emerald-600 dark:text-emerald-400 flex items-center gap-4 animate-slide-in">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest">Succès</p>
                        <p class="text-sm font-bold"><?php echo $message; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-8 p-6 bg-rose-50 dark:bg-rose-900/20 border border-rose-500/30 rounded-[2rem] text-rose-600 dark:text-rose-400 flex items-center gap-4 animate-slide-in">
                    <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest">Erreur</p>
                        <p class="text-sm font-bold"><?php echo $error; ?></p>
                    </div>
                </div>
            <?php endif; ?>

        <form action="" method="POST" class="space-y-12 pb-20">
            <?php
            // Génération de 20 sujets professionnels type "Notification"
            $random_subjects = [
                "📬 Nouvelles actions et projets de Ir. Samy Magadju",
                "⚡ Mise à jour : Les dernières réalisations sur le terrain",
                "📢 Annonce importante de la Dynamique Ir. Samy Magadju",
                "🎯 Nouveaux objectifs atteints - Découvrez nos actions",
                "🌟 Exclusif : Ce que nous avons accompli cette semaine",
                "✅ Bilan et Perspectives : L'actualité de Ir. Samy Magadju",
                "🚀 Impact direct : Vos actualités de la Dynamique",
                "💡 Innovation et Développement : Les nouveaux projets",
                "📈 Rapport d'activité : Avancées de Ir. Samy Magadju",
                "🤝 Ensemble sur le terrain : Nouvelles initiatives",
                "🔔 Notification : Nouvelles résolutions et projets en cours",
                "🗞️ La Une de la Dynamique : Actualités et Actions",
                "🔥 Priorité : Les dossiers urgents et réalisations",
                "📍 Point de situation : Les actions menées par Ir. Samy Magadju",
                "✨ À la une : Succès et prochaines étapes de la Dynamique",
                "🎙️ Communication officielle : Les avancées de nos projets",
                "🌍 Agir pour demain : Les récentes initiatives de Ir. Samy Magadju",
                "📊 Suivi des actions : Les résultats de cette période",
                "🛠️ Travail en cours : Découvrez les chantiers actuels",
                "📆 Agenda et Réalisations : Ce qu'il ne fallait pas manquer"
            ];
            $selected_subject = $random_subjects[array_rand($random_subjects)];
            ?>

            <!-- Sujet -->
            <section class="glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500 mb-6 flex items-center gap-3">
                    <span class="w-6 h-[1px] bg-slate-300"></span> Sujet & Configuration
                </h2>
                <div class="space-y-4">
                    <label for="subject" class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Sujet de l'email (Généré aléatoirement)</label>
                    <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($selected_subject); ?>" 
                        class="w-full px-6 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all font-bold">
                    <p class="text-xs text-slate-400 font-medium ml-1"> Ce sujet a été généré aléatoirement pour optimiser la délivrabilité. Vous pouvez le modifier si besoin.</p>
                </div>
            </section>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-12">
                    <!-- Abonnés -->
                    <section class="glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm flex flex-col">
                        <div class="flex flex-col gap-4 mb-8">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500 flex items-center gap-3">
                                    <span class="w-6 h-[1px] bg-slate-300"></span> Destinataires (<?php echo count($subscribers); ?>)
                                </h2>
                                <button type="button" onclick="selectAll('subscribers')" class="text-[10px] font-black text-amber-600 uppercase tracking-widest hover:underline">Tout sélectionner</button>
                            </div>
                            <div class="relative">
                                <input type="text" onkeyup="filterList('subscribers-list', this.value)" placeholder="Rechercher un abonné par email..." class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all text-slate-700 dark:text-slate-300 font-medium">
                                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                        <div id="subscribers-list" class="flex-1 space-y-2 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            <?php foreach ($subscribers as $sub): ?>
                                <div class="relative">
                                    <input type="checkbox" name="subscribers[]" value="<?php echo $sub['id']; ?>" id="sub_<?php echo $sub['id']; ?>" class="peer hidden">
                                    <label for="sub_<?php echo $sub['id']; ?>" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50/10">
                                        <div class="w-5 h-5 rounded-full border-2 border-slate-200 dark:border-slate-700 peer-checked:bg-amber-500 flex items-center justify-center transition-all">
                                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($sub['email']); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Articles & Projets -->
                    <section class="space-y-12">
                        <!-- News -->
                        <div class="glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm flex flex-col">
                            <div class="flex flex-col gap-4 mb-8">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500 flex items-center gap-3">
                                        <span class="w-6 h-[1px] bg-slate-300"></span> Actualités
                                    </h2>
                                    <button type="button" onclick="selectAll('news')" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Tout sélectionner</button>
                                </div>
                                <div class="relative">
                                    <input type="text" onkeyup="filterList('news-list', this.value)" placeholder="Rechercher une actualité..." class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all text-slate-700 dark:text-slate-300 font-medium">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                            <div id="news-list" class="space-y-4 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                                <?php foreach ($news_list as $n): ?>
                                    <?php 
                                        // On récupère l'image principale pour l'actualité
                                        $stmt_img = $pdo->prepare("SELECT image FROM news WHERE id = ?");
                                        $stmt_img->execute([$n['id']]);
                                        $img_row = $stmt_img->fetch();
                                        $img_src = ($img_row && $img_row['image']) ? SITE_URL.'/uploads/'.$img_row['image'] : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=100&q=80';
                                    ?>
                                    <div class="relative">
                                        <input type="checkbox" name="news[]" value="<?php echo $n['id']; ?>" id="news_<?php echo $n['id']; ?>" class="peer hidden">
                                        <label for="news_<?php echo $n['id']; ?>" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50/10">
                                            <div class="w-16 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Miniature" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200 block truncate"><?php echo htmlspecialchars($n['title']); ?></span>
                                                <span class="text-[10px] text-slate-400 font-medium uppercase mt-1 block"><?php echo formatDateFR($n['created_at']); ?></span>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-slate-200 dark:border-slate-700 peer-checked:bg-amber-500 peer-checked:border-amber-500 flex items-center justify-center transition-all opacity-50 peer-checked:opacity-100 shrink-0">
                                                <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Projects -->
                        <div class="glass-panel p-8 md:p-12 rounded-[3rem] shadow-sm flex flex-col">
                            <div class="flex flex-col gap-4 mb-8">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 dark:text-slate-500 flex items-center gap-3">
                                        <span class="w-6 h-[1px] bg-slate-300"></span> Projets
                                    </h2>
                                    <button type="button" onclick="selectAll('projects')" class="text-[10px] font-black text-amber-600 uppercase tracking-widest hover:underline">Tout sélectionner</button>
                                </div>
                                <div class="relative">
                                    <input type="text" onkeyup="filterList('projects-list', this.value)" placeholder="Rechercher un projet..." class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all text-slate-700 dark:text-slate-300 font-medium">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                            <div id="projects-list" class="space-y-4 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                                <?php foreach ($projects_list as $p): ?>
                                    <?php 
                                        // On récupère l'image principale pour le projet
                                        $stmt_img = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
                                        $stmt_img->execute([$p['id']]);
                                        $img_row = $stmt_img->fetch();
                                        $img_src = ($img_row && $img_row['image']) ? SITE_URL.'/uploads/'.$img_row['image'] : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=100&q=80';
                                    ?>
                                    <div class="relative">
                                        <input type="checkbox" name="projects[]" value="<?php echo $p['id']; ?>" id="proj_<?php echo $p['id']; ?>" class="peer hidden">
                                        <label for="proj_<?php echo $p['id']; ?>" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50/10">
                                            <div class="w-16 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Miniature" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200 block truncate"><?php echo htmlspecialchars($p['title']); ?></span>
                                                <span class="text-[10px] text-slate-400 font-medium uppercase mt-1 block"><?php echo formatDateFR($p['created_at']); ?></span>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-slate-200 dark:border-slate-700 peer-checked:bg-amber-500 peer-checked:border-amber-500 flex items-center justify-center transition-all opacity-50 peer-checked:opacity-100 shrink-0">
                                                <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 p-8 md:p-12 glass-panel rounded-[3rem] shadow-sm">
                    <button type="reset" class="px-8 py-5 bg-white dark:bg-slate-900 text-slate-400 font-bold rounded-[2rem] border border-slate-200 dark:border-slate-800 hover:text-rose-600 transition-all">
                        Réinitialiser
                    </button>
                    <button type="submit" class="px-10 py-5 bg-amber-600 text-white font-black rounded-[2rem] hover:bg-amber-700 transition-all shadow-xl shadow-amber-600/30 flex items-center gap-3 group">
                        <span>Lancer l'expédition</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function selectAll(type) {
            const checkboxes = document.querySelectorAll(`input[name='${type}[]']`);
            checkboxes.forEach(cb => {
                // Seulement s'ils sont visibles (non filtrés)
                const parent = cb.closest('.relative');
                if (parent && parent.style.display !== 'none') {
                    cb.checked = true;
                }
            });
        }

        function filterList(listId, searchTerm) {
            const list = document.getElementById(listId);
            const items = list.children;
            const term = searchTerm.toLowerCase();

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const text = item.innerText.toLowerCase();
                if (text.includes(term)) {
                    item.style.display = "";
                } else {
                    item.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
