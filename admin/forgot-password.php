<?php
/**
 * admin/forgot-password.php
 * Système de réinitialisation de mot de passe "Premium"
 */
require_once '../config/database.php';
require_once '../config/mail.php';
require_once '../config/helpers.php';
// require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/router.php';

$pdo = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse mail valide.";
    } else {
        // Vérifier si l'utilisateur existe (uniquement les Éditeurs pour cette procédure)
        $stmt = $pdo->prepare("SELECT id, full_name, role FROM users WHERE email = :email AND role = 'Editor'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Générer un nouveau mot de passe sécurisé
            $newPassword = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*'), 0, 12);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Mettre à jour dans la DB
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute([':password' => $hashedPassword, ':id' => $user['id']]);
            
            // 1. Envoyer le mail d'alerte
            $alertSubject = "Alerte de sécurité : Réinitialisation de mot de passe";
            $alertBody = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
                    <h2 style='color: #E11D48;'>Alerte de sécurité</h2>
                    <p>Bonjour <strong>{$user['full_name']}</strong>,</p>
                    <p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte sur la plateforme <strong>DSM Admin</strong>.</p>
                    <p style='color: #666;'>Si vous n'êtes pas à l'origine de cette demande, veuillez contacter l'administrateur système immédiatement.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>Ceci est une notification automatique de sécurité.</p>
                </div>";
            
            sendMail($email, $alertSubject, $alertBody);
            
            // 2. Envoyer le mail avec le nouveau mot de passe
            $resetSubject = "Vos nouveaux identifiants DSM Admin";
            $resetBody = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; overflow: hidden; border-radius: 12px;'>
                    <div style='background: #7C3AED; padding: 30px; text-align: center; color: white;'>
                        <h1 style='margin: 0; font-size: 24px;'>Nouveau Mot de Passe</h1>
                    </div>
                    <div style='padding: 30px; line-height: 1.6;'>
                        <p>Bonjour <strong>{$user['full_name']}</strong>,</p>
                        <p>Votre mot de passe a été réinitialisé avec succès. Voici vos nouveaux identifiants :</p>
                        <div style='background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #7C3AED;'>
                            <p style='margin: 0;'><strong>Identifiant :</strong> {$email}</p>
                            <p style='margin: 10px 0;'><strong>Mot de passe :</strong> <code style='background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 16px; font-weight: bold; color: #7C3AED;'>{$newPassword}</code></p>
                        </div>
                        <p>Pour des raisons de sécurité, nous vous conseillons de <strong>changer ce mot de passe</strong> dès votre première connexion.</p>
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='".SITE_URL."/admin/connexion' style='background: #7C3AED; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Se connecter maintenant</a>
                        </div>
                    </div>
                    <div style='background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b;'>
                        &copy; " . date('Y') . " DSM - Direction Système et Management. Sécurité Garantie.
                    </div>
                </div>";
            
            if (sendMail($email, $resetSubject, $resetBody)) {
                $message = "Votre nouveau mot de passe a été envoyé avec succès à votre adresse mail.";
            } else {
                $error = "Le mot de passe a été réinitialisé mais l'envoi du mail a échoué. Contactez l'administrateur.";
            }
        } else {
            $error = "Email ou compte introuvable parmi les éditeurs.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation — DSM Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="bg-[#0F0A1E] min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Décorations d'arrière-plan -->
    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-purple-600 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-lg relative z-10">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl">
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/20">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none">Réinitialisation</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mt-3">Sécurité & Accès</p>
            </div>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block ml-2">Adresse Mail</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none group-focus-within:text-emerald-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input type="email" name="email" required placeholder="nom@exemple.com" 
                            class="w-full pl-14 pr-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl text-slate-900 font-bold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all placeholder:text-slate-300">
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Renvoyer un mot de passe
                </button>

                <div class="text-center pt-4">
                    <a href="connexion" class="text-[11px] font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Retour à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Interface Toast -->
    <div id="toast-container" class="fixed top-12 right-12 z-[100] flex flex-col gap-4"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-emerald-600 shadow-emerald-500/30',
                error: 'bg-rose-600 shadow-rose-500/30'
            };

            toast.className = `${colors[type]} text-white px-8 py-5 rounded-[1.5rem] shadow-2xl flex items-center gap-5 transform translate-x-full opacity-0 transition-all duration-500 min-w-[340px] border border-white/20 backdrop-blur-xl animate-in fade-in slide-in-from-right-full`;
            
            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    ${type === 'success' ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' : 
                      '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'}
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">${type === 'success' ? 'Système' : 'Erreur'}</p>
                    <p class="text-sm font-bold leading-tight">${message}</p>
                </div>
            `;

            container.appendChild(toast);
            setTimeout(() => { toast.classList.remove('translate-x-full', 'opacity-0'); }, 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 6000);
        }

        <?php if ($message): ?>
            showToast("<?php echo addslashes($message); ?>", "success");
        <?php endif; ?>

        <?php if ($error): ?>
            showToast("<?php echo addslashes($error); ?>", "error");
        <?php endif; ?>
    </script>
</body>
</html>
