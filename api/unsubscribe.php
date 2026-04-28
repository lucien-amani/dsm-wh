<?php
require_once '../config/database.php';
require_once '../config/router.php';
require_once '../config/helpers.php';
require_once '../config/database.php';
require_once '../config/helpers.php';

$message = '';
$error = '';

if (isset($_GET['email'])) {
    $email = base64_decode($_GET['email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Vous avez été désabonné avec succès de notre newsletter.";
            } else {
                $error = "Cette adresse email n'est pas inscrite dans notre liste ou est déjà désabonnée.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur est survenue lors du désabonnement.";
        }
    } else {
        $error = "Lien de désabonnement invalide.";
    }
} else {
    $error = "Lien de désabonnement manquant.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Désabonnement - DSM</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-6 right-6 z-[300] flex flex-col gap-3 pointer-events-none"></div>

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 p-10 text-center border border-slate-100 animate-fade-in">
        <div class="w-20 h-20 <?php echo $message ? 'bg-emerald-50' : 'bg-rose-50'; ?> rounded-3xl flex items-center justify-center mx-auto mb-8">
            <?php if ($message): ?>
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <?php else: ?>
                <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            <?php endif; ?>
        </div>
        
        <h1 class="text-2xl font-black text-slate-900 mb-4 uppercase tracking-tighter">
            <?php echo $message ? 'Désabonnement' : 'Oups !'; ?>
        </h1>
        
        <p class="text-slate-600 font-medium leading-relaxed mb-8">
            <?php echo $message ?: $error; ?>
        </p>
        
        <a href="<?php echo SITE_URL; ?>" class="inline-block px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-slate-800 transition-all shadow-lg active:scale-95">
            Retour à l'accueil
        </a>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($message): ?>
                showToast("<?php echo $message; ?>", 'warning');
            <?php elseif ($error): ?>
                showToast("<?php echo $error; ?>", 'error');
            <?php endif; ?>
        });
    </script>
</body>
</html>
