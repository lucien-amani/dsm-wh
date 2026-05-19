<?php
require_once '../config/database.php';
require_once '../config/mail.php';
require_once '../config/helpers.php';
require_once __DIR__ . '/../config/router.php';
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
    $id_param = $_GET['delete'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    // On ne peut pas supprimer soi-même
    if ($id && $id !== (int)$_SESSION['admin_id']) {
        $check = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $check->execute([$id]);
        $name = $check->fetchColumn() ?: "ID $id";

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'Editor'");
        $stmt->execute([':id' => $id]);
        
        logAdminAction($_SESSION['admin_id'], "suppression éditeur : $name");
        header('Location: ' . SITE_URL . '/admin/utilisateurs?msg=deleted');
        exit;
    }
}

// Action: Toggle Status (Activer/Désactiver)
if (isset($_GET['toggle'])) {
    $id_param = $_GET['toggle'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id && $id !== (int)$_SESSION['admin_id']) {
        $stmt = $pdo->prepare("UPDATE users SET status = 1 - status WHERE id = :id AND role = 'Editor'");
        $stmt->execute([':id' => $id]);
        
        $check = $pdo->prepare("SELECT full_name, status FROM users WHERE id = ?");
        $check->execute([$id]);
        $res = $check->fetch();
        $status_txt = $res['status'] ? 'activation' : 'désactivation';
        
        logAdminAction($_SESSION['admin_id'], "$status_txt éditeur : " . $res['full_name']);
        header('Location: ' . SITE_URL . '/admin/utilisateurs?msg=updated');
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
                    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, username, password, role, status) VALUES (:full_name, :email, :phone, :username, :password, 'Editor', 1)");
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
    $edit_id_param = $_GET['edit'];
    $edit_id = is_numeric($edit_id_param) ? (int)$edit_id_param : ($hashids->decode($edit_id_param)[0] ?? 0);
    
    if ($edit_id) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'Editor'");
        $stmt->execute([':id' => $edit_id]);
        $edit_editor = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-panel { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); }
        
        .input-clean {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.5);
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 1.2rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s ease;
        }
        .dark .input-clean {
            background: rgba(15, 23, 42, 0.3);
            border-color: rgba(255,255,255,0.05);
            color: #f8fafc;
        }
        .input-clean:focus {
            background: #fff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }
        .dark .input-clean:focus {
            background: rgba(15, 23, 42, 0.6);
        }
        
        .label-clean {
            font-size: 0.7rem;
            font-weight: 900;
            color: #94a3b8;
            margin-bottom: 0.6rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-left: 0.5rem;
        }
        
        .btn-clean {
            padding: 1.1rem 2rem;
            background: #10b981;
            color: #fff;
            border-radius: 1.5rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.3);
        }
        .btn-clean:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
            filter: brightness(1.1);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            box-shadow: none;
        }
        .dark .btn-secondary {
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.65rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen font-inter">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <!-- Header -->
        <?php 
        $header_title = 'Gestion de <span class="text-emerald-600">l\'Équipe</span>';
        $header_subtitle = 'Éditeurs & Collaborateurs';
        $header_actions = '';
        if (!isset($_GET['add']) && !$edit_editor) {
            $header_actions = '
                <a href="?add=1" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2.5 lg:px-6 lg:py-4 rounded-xl font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg shadow-emerald-600/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Nouveau Membre</span>
                </a>';
        }
        include 'includes/header.php'; 
        ?>

        <div class="p-8 lg:p-12 animate-fade-in">
            <?php if (isset($_GET['add']) || $edit_editor): ?>
                <!-- FORM VIEW (Similaire au profil) -->
                <div class="max-w-4xl mx-auto space-y-8">
                    <!-- Header avec bouton retour -->
                    <div class="flex items-center justify-between mb-8">
                        <a href="utilisateurs" class="group flex items-center gap-3 px-6 py-3 rounded-2xl bg-white dark:bg-slate-900 text-slate-500 hover:text-emerald-600 border border-slate-200 dark:border-slate-800 transition-all">
                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">Retour à la liste</span>
                        </a>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] block mb-1">Configuration</span>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase"><?php echo $edit_editor ? 'Édition Profil' : 'Nouveau Membre'; ?></h2>
                        </div>
                    </div>

                    <form method="POST" id="userForm" class="space-y-8">
                        <input type="hidden" name="editor_id" value="<?php echo $edit_editor['id'] ?? ''; ?>">
                        
                        <!-- Identity Card -->
                        <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 text-center relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative w-32 h-32 mx-auto mb-6">
                                <div class="w-full h-full rounded-[2.5rem] bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-4xl font-black text-emerald-600 border-4 border-white dark:border-slate-800 shadow-xl">
                                    <?php echo strtoupper(substr($edit_editor['full_name'] ?? 'N', 0, 1)); ?>
                                </div>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo htmlspecialchars($edit_editor['full_name'] ?? 'Nouveau Membre'); ?></h2>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1">Éditeur DSM</p>
                        </div>

                        <!-- Fields Card -->
                        <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nom Complet</label>
                                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($edit_editor['full_name'] ?? ''); ?>" placeholder="Jean Dupont"
                                           class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Identifiant (Username)</label>
                                    <input type="text" name="username" required value="<?php echo htmlspecialchars($edit_editor['username'] ?? ''); ?>" placeholder="jdupont"
                                           class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">E-mail Professionnel</label>
                                    <input type="email" name="email" required value="<?php echo htmlspecialchars($edit_editor['email'] ?? ''); ?>" placeholder="jean@dsm.com"
                                           class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Téléphone</label>
                                    <input type="text" name="phone" value="<?php echo htmlspecialchars($edit_editor['phone'] ?? ''); ?>" placeholder="+243..."
                                           class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50 space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Mot de passe <?php echo $edit_editor ? '(Laissez vide pour conserver)' : ''; ?></label>
                                <div class="relative group">
                                    <input type="password" name="password" id="password_input" <?php echo $edit_editor ? '' : 'required'; ?> placeholder="••••••••"
                                           class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                                    <button type="button" onclick="generatePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:border-emerald-600 transition-all shadow-sm" title="Générer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-4">
                                <a href="utilisateurs" class="px-8 py-5 text-slate-400 font-black uppercase tracking-widest text-[10px] hover:text-rose-600 transition-all">
                                    Annuler
                                </a>
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-5 rounded-[2rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-600/20 hover:-translate-y-1 transition-all">
                                    <?php echo $edit_editor ? 'Mettre à jour le membre' : 'Enregistrer le membre'; ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- LIST VIEW -->
                <div class="grid grid-cols-1 gap-12">
                    <div class="col-span-1">
                        <div class="mb-10 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-black uppercase tracking-[0.3em] text-emerald-600 mb-2">Équipe active</h2>
                                <p class="text-3xl font-black text-slate-900 dark:text-white"><?php echo count($editors); ?> <span class="text-slate-400">Éditeurs</span></p>
                            </div>
                            <a href="?add=1" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-4 rounded-[1.5rem] font-black uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-600/20 hover:-translate-y-1 transition-all flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Nouveau Membre
                            </a>
                        </div>

                        <div class="glass-panel rounded-[3rem] shadow-sm overflow-hidden overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Collaborateur</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden md:table-cell">Contact & Accès</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    <?php foreach ($editors as $ed): ?>
                                        <tr class="hover:bg-emerald-50/10 dark:hover:bg-emerald-900/5 transition-all group">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-5">
                                                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-black text-xl shadow-inner group-hover:scale-110 transition-transform duration-500">
                                                        <?php echo substr($ed['full_name'], 0, 1); ?>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-base font-black text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($ed['full_name']); ?></span>
                                                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-0.5">Membre depuis <?php echo date('M Y', strtotime($ed['created_at'])); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 hidden md:table-cell">
                                                <div class="flex flex-col gap-1">
                                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($ed['email']); ?></span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-black text-emerald-600 lowercase bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-md">@<?php echo htmlspecialchars($ed['username']); ?></span>
                                                        <span class="text-[10px] font-bold text-slate-400"><?php echo htmlspecialchars($ed['phone']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <a href="?toggle=<?php echo $hashids->encode($ed['id']); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border transition-all <?php echo ($ed['status'] ?? 1) ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600' : 'bg-rose-50 dark:bg-rose-900/10 border-rose-500/10 text-rose-500'; ?>">
                                                    <div class="w-2 h-2 rounded-full <?php echo ($ed['status'] ?? 1) ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50 animate-pulse' : 'bg-rose-500'; ?>"></div>
                                                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo ($ed['status'] ?? 1) ? 'Actif' : 'Désactivé'; ?></span>
                                                </a>
                                            </td>
                                            <td class="px-8 py-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                                    <a href="?edit=<?php echo $hashids->encode($ed['id']); ?>" class="w-11 h-11 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-lg hover:shadow-blue-500/20 border border-blue-500/10" title="Modifier">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </a>
                                                    <a href="?delete=<?php echo $hashids->encode($ed['id']); ?>" onclick="event.preventDefault(); showConfirm('Confirmer la suppression ?', () => window.location.href=this.href);" class="w-11 h-11 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10" title="Supprimer">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($editors)): ?>
                                        <tr>
                                            <td colspan="4" class="px-8 py-24 text-center text-slate-400 italic font-bold">Aucun membre enregistré dans l'équipe.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function generatePassword() {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let password = "";
            for (let i = 0; i < 12; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const input = document.getElementById('password_input');
            input.value = password;
            input.type = 'text';
            input.select();
            
            showToast('Mot de passe généré avec succès', 'success');
        }

        <?php if ($success): ?>
            showToast(<?php echo json_encode($success); ?>, 'success');
        <?php endif; ?>
        <?php if ($error): ?>
            showToast(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>
