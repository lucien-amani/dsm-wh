<?php
require_once '../config/database.php';
require_once '../config/mail.php';
require_once '../config/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'includes/auth.php';

// Seul un Superadmin ou Admin peut accéder à la gestion des éditeurs
if ($_SESSION['admin_role'] !== 'Superadmin' && $_SESSION['admin_role'] !== 'Admin') {
    header('Location: ' . SITE_URL . '/admin/tableau-de-bord');
    exit;
}

$pdo = getDBConnection();
$error = '';
$success = '';

// Action: Supprimer un éditeur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // On ne peut pas supprimer soi-même
    if ($id !== (int)$_SESSION['admin_id']) {
        // Optionnel : récupérer le nom avant suppression
        $check = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $check->execute([$id]);
        $name = $check->fetchColumn() ?: "ID $id";

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'Editor'");
        $stmt->execute([':id' => $id]);
        
        logAdminAction($_SESSION['admin_id'], "suppression éditeur : $name");
        header('Location: ' . SITE_URL . '/admin/editors?msg=deleted');
        exit;
    }
}

// Action: Ajouter/Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_id = $_POST['editor_id'] ?? null;
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($full_name && $email && $username) {
        try {
            if ($editor_id) {
                // Modification
                $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, username = :username";
                $params = [
                    ':full_name' => $full_name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':username' => $username,
                    ':id' => $editor_id
                ];
                if (!empty($password)) {
                    $sql .= ", password = :password";
                    $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id = :id AND role = 'Editor'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                logAdminAction($_SESSION['admin_id'], "modification éditeur : $full_name");
                $success = "Éditeur mis à jour avec succès.";
            } else {
                // Ajout
                if (empty($password)) {
                    $error = "Le mot de passe est requis pour un nouvel éditeur.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, username, password, role) VALUES (:full_name, :email, :phone, :username, :password, 'Editor')");
                    $stmt->execute([
                        ':full_name' => $full_name,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':username' => $username,
                        ':password' => password_hash($password, PASSWORD_DEFAULT)
                    ]);
                    logAdminAction($_SESSION['admin_id'], "création éditeur : $full_name");
                    $success = "Nouvel éditeur créé avec succès.";

                    // --- ENVOI DE L'EMAIL DE BIENVENUE (Design Google Type) ---
                    $login_url = SITE_URL . "/admin/connexion";
                    $site_logo = SITE_URL . "/assets/logo/logo-dsm.jpg";
                    
                    $email_body = "
                    <div style='font-family: \"Inter\", -apple-system, sans-serif; background-color: #f8fafc; padding: 60px 20px; color: #1e293b;'>
                        <div style='max-width: 550px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);'>
                            <div style='padding: 50px 40px;'>
                                <div style='text-align: center; margin-bottom: 35px;'>
                                    <img src='{$site_logo}' alt='DSM Logo' style='width: 70px; height: 70px; border-radius: 18px; margin-bottom: 20px;'>
                                    <h2 style='font-family: \"Outfit\", sans-serif; font-size: 26px; font-weight: 900; color: #0f172a; margin: 0; letter-spacing: -0.025em; text-transform: uppercase;'>Bienvenue dans l'équipe</h2>
                                </div>
                                
                                <p style='font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 30px;'>
                                    Bonjour <b>{$full_name}</b>,<br><br>
                                    Votre espace de travail est prêt. Vous avez été officiellement ajouté en tant qu'<b>Éditeur</b> sur la plateforme administrative de la Dynamique Samy Magadju.
                                </p>

                                <div style='background: #f1f5f9; border-radius: 16px; padding: 25px; margin-bottom: 30px;'>
                                    <p style='font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 15px 0;'>Vos accès sécurisés</p>
                                    <div style='margin-bottom: 12px;'>
                                        <span style='font-size: 13px; color: #94a3b8; display: block; margin-bottom: 4px;'>Identifiant (Email / Tel)</span>
                                        <span style='font-size: 15px; color: #1e293b; font-weight: 700;'>{$email}</span>
                                    </div>
                                    <div>
                                        <span style='font-size: 13px; color: #94a3b8; display: block; margin-bottom: 4px;'>Mot de passe provisoire</span>
                                        <span style='font-size: 17px; color: #4f46e5; font-family: monospace; font-weight: 800; letter-spacing: 1px; background: #fff; padding: 4px 12px; border-radius: 8px; border: 1px solid #e2e8f0;'>{$password}</span>
                                    </div>
                                </div>

                                <div style='text-align: center; margin-bottom: 35px;'>
                                    <a href='{$login_url}' style='display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 16px 35px; border-radius: 14px; font-size: 14px; font-weight: 800; text-decoration: none; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);'>Accéder à mon espace</a>
                                </div>

                                <div style='padding: 20px; background: #fff1f2; border-radius: 14px; border: 1px solid #fecaca; margin-bottom: 0;'>
                                    <div style='display: flex; align-items: flex-start; gap: 12px;'>
                                        <span style='font-size: 18px;'>⚠️</span>
                                        <p style='font-size: 13px; color: #991b1b; font-weight: 600; margin: 0; line-height: 1.5;'>
                                            <b>Action requise :</b> Pour garantir la sécurité de vos données, il est impératif de modifier ce mot de passe dès votre première connexion dans l'onglet \"Profil\".
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div style='background: #f8fafc; padding: 30px 40px; text-align: center; border-top: 1px solid #f1f5f9;'>
                                <p style='font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.6;'>
                                    Ceci est une notification automatique. Merci de ne pas répondre.<br>
                                    &copy; " . date('Y') . " Dynamique Samy Magadju - Administration
                                </p>
                            </div>
                        </div>
                    </div>
                    ";

                    if (sendMail($email, "Activation de votre compte Éditeur - DSM Admin", $email_body)) {
                        $success .= " Un email de bienvenue a été envoyé à l'adresse fournie.";
                    } else {
                        $success .= " (Attention : L'email de bienvenue n'a pas pu être envoyé).";
                    }
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir les champs obligatoires.";
    }
}

$editors = $pdo->query("SELECT * FROM users WHERE role = 'Editor' ORDER BY created_at DESC")->fetchAll();
$current_page = 'editors';

// Si édition, on récupère les infos
$edit_editor = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'Editor'");
    $stmt->execute([':id' => $_GET['edit']]);
    $edit_editor = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Équipe - DSM ADMIN</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-panel { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .dark .glass-panel { 
            background: rgba(15, 23, 42, 0.8); 
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        .input-premium {
            width: 100%;
            padding-left: 3rem;
            padding-right: 1.25rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            outline: none;
        }
        .dark .input-premium {
            background-color: rgba(15, 23, 42, 0.5);
            border-color: #1e293b;
            color: #fff;
        }
        .input-premium:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            border-color: #4f46e5;
        }
        .label-premium {
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            margin-left: 0.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        .dark .label-premium {
            color: #64748b;
        }
        .btn-premium {
            position: relative;
            overflow: hidden;
            padding: 1rem 2rem;
            background-color: #4f46e5;
            color: #fff;
            font-weight: 900;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 10px 10px -5px rgba(79, 70, 229, 0.04);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            border: none;
            cursor: pointer;
        }
        .btn-premium:hover {
            box-shadow: 0 25px 30px -5px rgba(79, 70, 229, 0.2);
            transform: translateY(-2px);
            background-color: #4338ca;
        }
        .btn-premium:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0">
        <!-- Modern Header -->
        <header class="h-28 flex items-center justify-between px-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none">Gestion <span class="text-indigo-600">Équipe</span></h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] mt-2">Membres & Rôles</p>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-3 px-6 py-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-sm">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">Système Opérationnel</span>
            </div>
        </header>

        <div class="p-10 space-y-10 animate-fade-in max-w-[1600px] mx-auto w-full">
            
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
                
                <!-- Registration Form Card -->
                <div class="xl:col-span-4">
                    <div class="glass-panel p-10 rounded-[3.5rem] sticky top-36">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                <?php echo $edit_editor ? 'Mise à Jour' : 'Ajouter un Membre'; ?>
                            </h2>
                        </div>
                        
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="editor_id" value="<?php echo $edit_editor['id'] ?? ''; ?>">
                            
                            <!-- Full Name -->
                            <div class="relative group">
                                <label class="label-premium">Nom Complet</label>
                                <div class="absolute bottom-4 left-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text" name="full_name" required placeholder="Ex: Jean-Luc Kivu" value="<?php echo htmlspecialchars($edit_editor['full_name'] ?? ''); ?>" class="input-premium">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Email -->
                                <div class="relative group">
                                    <label class="label-premium">E-mail Professionnel</label>
                                    <div class="absolute bottom-4 left-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" name="email" required placeholder="contact@dsm.cd" value="<?php echo htmlspecialchars($edit_editor['email'] ?? ''); ?>" class="input-premium">
                                </div>

                                <!-- Phone -->
                                <div class="relative group">
                                    <label class="label-premium">Téléphone</label>
                                    <div class="absolute bottom-4 left-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input type="text" name="phone" placeholder="+243..." value="<?php echo htmlspecialchars($edit_editor['phone'] ?? ''); ?>" class="input-premium">
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="relative group">
                                <label class="label-premium">Nom d'Utilisateur</label>
                                <div class="absolute bottom-4 left-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <input type="text" name="username" required placeholder="ex: j.kivu" value="<?php echo htmlspecialchars($edit_editor['username'] ?? ''); ?>" class="input-premium">
                            </div>

                            <!-- Password -->
                            <div class="relative group">
                                <label class="label-premium">Sécurité <?php echo $edit_editor ? '(Laissez vide si inchangé)' : ''; ?></label>
                                <div class="absolute bottom-4 left-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password" id="password_input" <?php echo $edit_editor ? '' : 'required'; ?> placeholder="••••••••••••"
                                       class="input-premium pr-14">
                                <button type="button" onclick="generatePassword()" class="absolute right-3 bottom-2.5 w-9 h-9 flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Générer un mot de passe">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="btn-premium w-full">
                                    <span><?php echo $edit_editor ? 'Mettre à jour le membre' : 'Valider l\'inscription'; ?></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                </button>
                                <?php if($edit_editor): ?>
                                    <a href="editors" class="mt-4 block text-center text-[10px] font-black text-slate-400 hover:text-indigo-600 uppercase tracking-[0.2em] transition-colors italic">Annuler la modification</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Members List Table -->
                <div class="xl:col-span-8">
                    <div class="glass-panel p-2 rounded-[3.5rem] overflow-hidden">
                        <div class="p-8 pb-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Liste de l'Équipe</h3>
                            </div>
                            <span class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-[10px] font-black text-slate-500 rounded-xl uppercase tracking-widest">
                                <?php echo count($editors); ?> Membre(s)
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[800px]">
                                <thead>
                                    <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-indigo-50 dark:border-slate-800/50">
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identité</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Coordonnées</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Accès</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right pr-12">Gestion</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-indigo-50/30 dark:divide-slate-800/50">
                                    <?php if (empty($editors)): ?>
                                        <tr>
                                            <td colspan="4" class="px-8 py-24 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 mb-6">
                                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 20m0 0c1.398 0 2.735-.308 3.935-.856m1.545-2.903a10.011 10.011 0 001.202-2.59M12 10a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                                    </div>
                                                    <p class="text-slate-400 font-bold font-outfit uppercase tracking-widest text-sm italic">Aucun membre enregistré.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php foreach ($editors as $ed): ?>
                                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/5 transition-all group">
                                            <td class="px-8 py-8">
                                                <div class="flex items-center gap-5">
                                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/20 transform group-hover:rotate-6 transition-transform">
                                                        <?php echo strtoupper(substr($ed['full_name'], 0, 1)); ?>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="font-extrabold text-[15px] text-slate-900 dark:text-white leading-tight mb-1"><?php echo htmlspecialchars($ed['full_name']); ?></span>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded text-[9px] font-black uppercase tracking-widest italic">Éditeur</span>
                                                            <span class="text-[10px] text-slate-400 font-bold">@<?php echo htmlspecialchars($ed['username']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-8">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-2 group/link">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400"><?php echo htmlspecialchars($ed['email']); ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        <span class="text-[10px] font-black text-indigo-600/80 tracking-widest"><?php echo htmlspecialchars($ed['phone'] ?: 'No Phone'); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-8 text-center">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-tighter">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                                    Actif
                                                </span>
                                            </td>
                                            <td class="px-8 py-8 text-right pr-12">
                                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                                    <a href="?edit=<?php echo $ed['id']; ?>" class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm hover:shadow-blue-600/20 border border-blue-100 dark:border-blue-900/50" title="Éditer le profil">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </a>
                                                    <button onclick="showConfirm('Attention ! Cette action est irréversible. Confirmer la suppression de ce membre ?', () => window.location.href='?delete=<?php echo $ed['id']; ?>')"
                                                       class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm hover:shadow-rose-600/20 border border-rose-100 dark:border-rose-900/50" title="Résilier l'accès">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function generatePassword() {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*";
            let password = "";
            for (let i = 0; i < 14; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const input = document.getElementById('password_input');
            input.value = password;
            input.type = 'text'; // Make it visible
            
            // Subtle flash effect to show it was generated
            input.style.transition = 'none';
            input.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
            setTimeout(() => {
                input.style.transition = 'all 0.3s ease';
                input.style.backgroundColor = '';
            }, 150);
        }

        // Show toasts from PHP variables
        <?php if ($success): ?>
            showToast(<?php echo json_encode($success); ?>, 'success');
        <?php endif; ?>

        <?php if ($error): ?>
            showToast(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>
