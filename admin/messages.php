<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Supprimer (avec support AJAX)
if (isset($_GET['delete'])) {
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur de sécurité : Jeton CSRF invalide.']);
            exit;
        }
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    $id_param = $_GET['delete'];
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Message supprimé avec succès.']);
            exit;
        }
        
        header('Location: ' . SITE_URL . '/admin/messages?msg=deleted');
        exit;
    }
}

// Action: Changer le statut (avec support AJAX)
if (isset($_GET['status']) && isset($_GET['id'])) {
    if (!validateCsrfToken($_GET['csrf'] ?? '')) {
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur de sécurité : Jeton CSRF invalide.']);
            exit;
        }
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }
    $id_param = $_GET['id'];
    $new_status = $_GET['status'];
    $allowed_status = ['Nouveau', 'Lu', 'Traité'];
    
    $id = is_numeric($id_param) ? (int)$id_param : ($hashids->decode($id_param)[0] ?? 0);
    
    if ($id && in_array($new_status, $allowed_status)) {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $new_status, ':id' => $id]);
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès.', 'new_status' => $new_status]);
            exit;
        }
        
        header('Location: ' . SITE_URL . '/admin/messages?msg=updated');
        exit;
    }
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$current_page = 'messages';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-panel { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .message-row { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .message-row:hover { transform: translateX(5px); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <!-- Header -->
        <?php 
        $header_title = 'Messages <span class="text-emerald-600">Reçus</span>';
        $header_subtitle = 'Communication & Feedback';
        $header_actions = '
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <span id="messages-total-count" class="block text-lg font-black text-slate-900 dark:text-white leading-none">'.count($messages).'</span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                </div>
            </div>';
        include 'includes/header.php'; 
        ?>

        <div class="p-8 lg:p-12 space-y-8 animate-fade-in">
            <!-- Messages List -->
            <div class="space-y-4">
                <?php foreach($messages as $msg): ?>
                <?php $msg_encoded = $hashids->encode($msg['id']); ?>
                <div class="message-row glass-panel rounded-[2rem] p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center gap-6 group" id="msg-row-<?php echo $msg_encoded; ?>">
                    <!-- Status Indicator -->
                    <div class="shrink-0 flex items-center gap-3">
                        <div class="relative inline-block group/dropdown">
                            <button id="status-btn-<?php echo $msg_encoded; ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all text-[10px] font-black uppercase tracking-widest
                                <?php 
                                    if($msg['status'] === 'Nouveau') echo 'bg-blue-50 dark:bg-blue-900/20 border-blue-500/20 text-blue-600';
                                    elseif($msg['status'] === 'Lu') echo 'bg-amber-50 dark:bg-amber-900/20 border-amber-500/20 text-amber-600';
                                    else echo 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500/20 text-emerald-600';
                                ?>">
                                <div id="status-dot-<?php echo $msg_encoded; ?>" class="status-dot 
                                    <?php 
                                        if($msg['status'] === 'Nouveau') echo 'bg-blue-500 shadow-lg shadow-blue-500/50 animate-pulse';
                                        elseif($msg['status'] === 'Lu') echo 'bg-amber-500';
                                        else echo 'bg-emerald-500';
                                    ?>"></div>
                                <span id="status-text-<?php echo $msg_encoded; ?>"><?php echo $msg['status']; ?></span>
                                <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute left-0 mt-2 w-32 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 opacity-0 invisible group-hover/dropdown:opacity-100 group-hover/dropdown:visible transition-all z-50 overflow-hidden">
                                <a href="?status=Nouveau&id=<?php echo $msg_encoded; ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" data-status="Nouveau" data-msg-id="<?php echo $msg_encoded; ?>" class="status-link block px-4 py-2 text-[10px] font-black uppercase tracking-widest text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">Nouveau</a>
                                <a href="?status=Lu&id=<?php echo $msg_encoded; ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" data-status="Lu" data-msg-id="<?php echo $msg_encoded; ?>" class="status-link block px-4 py-2 text-[10px] font-black uppercase tracking-widest text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">Lu</a>
                                <a href="?status=Traité&id=<?php echo $msg_encoded; ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" data-status="Traité" data-msg-id="<?php echo $msg_encoded; ?>" class="status-link block px-4 py-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">Traité</a>
                            </div>
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('d M Y, H:i', strtotime($msg['created_at'])); ?></span>
                    </div>

                    <!-- Expéditeur -->
                    <div class="lg:w-48 shrink-0">
                        <div class="flex flex-col">
                            <span class="font-black text-slate-900 dark:text-white uppercase tracking-tighter text-sm line-clamp-1"><?php echo htmlspecialchars($msg['name']); ?></span>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 truncate"><?php echo htmlspecialchars($msg['email']); ?></span>
                            <?php if($msg['phone']): ?>
                                <span class="text-[10px] font-medium text-slate-400"><?php echo htmlspecialchars($msg['phone']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="flex-1">
                        <div class="flex flex-col gap-1">
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm"><?php echo htmlspecialchars($msg['subject']); ?></span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="shrink-0 flex items-center justify-end gap-3 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        <a href="mailto:<?php echo $msg['email']; ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-lg hover:shadow-emerald-500/20 border border-emerald-500/10" title="Répondre">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </a>
                        <a href="?delete=<?php echo $msg_encoded; ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" onclick="event.preventDefault(); showConfirm('Supprimer ce message ?', () => deleteMessageAJAX('<?php echo $msg_encoded; ?>', '<?php echo $_SESSION['csrf_token']; ?>'));" class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20 border border-rose-500/10" title="Supprimer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($messages)): ?>
                    <div id="empty-state-panel" class="glass-panel rounded-[3rem] p-20 text-center space-y-4">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 5-8-5"/></svg>
                        </div>
                        <p class="text-slate-400 italic font-bold uppercase tracking-widest text-[10px]">Aucun message dans votre boîte de réception.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- AJAX JS Script Integration -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Changement de Statut avec AJAX
        const statusLinks = document.querySelectorAll('.status-link');
        statusLinks.forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const msgId = link.getAttribute('data-msg-id');
                const newStatus = link.getAttribute('data-status');
                const targetUrl = link.getAttribute('href');
                
                const statusBtn = document.getElementById(`status-btn-${msgId}`);
                const statusDot = document.getElementById(`status-dot-${msgId}`);
                const statusText = document.getElementById(`status-text-${msgId}`);
                
                try {
                    const response = await fetch(targetUrl + '&ajax=1');
                    if (!response.ok) throw new Error('Erreur réseau');
                    
                    const result = await response.json();
                    if (result.success) {
                        // Mettre à jour le texte du statut
                        statusText.textContent = newStatus;
                        
                        // Réinitialiser les classes du bouton et du point
                        statusBtn.className = 'flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all text-[10px] font-black uppercase tracking-widest';
                        statusDot.className = 'status-dot';
                        
                        if (newStatus === 'Nouveau') {
                            statusBtn.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-500/20', 'text-blue-600');
                            statusDot.classList.add('bg-blue-500', 'shadow-lg', 'shadow-blue-500/50', 'animate-pulse');
                        } else if (newStatus === 'Lu') {
                            statusBtn.classList.add('bg-amber-50', 'dark:bg-amber-900/20', 'border-amber-500/20', 'text-amber-600');
                            statusDot.classList.add('bg-amber-500');
                        } else {
                            statusBtn.classList.add('bg-emerald-50', 'dark:bg-emerald-900/20', 'border-emerald-500/20', 'text-emerald-600');
                            statusDot.classList.add('bg-emerald-500');
                        }
                        
                        showToast('Parfait ! Statut mis à jour.', 'success');
                    } else {
                        showToast(result.message || 'Erreur lors de la mise à jour.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    showToast('Une erreur réseau est survenue.', 'error');
                }
            });
        });
    });

    // 2. Suppression de Message avec AJAX
    async function deleteMessageAJAX(msgId, csrfToken) {
        const row = document.getElementById(`msg-row-${msgId}`);
        const totalCountSpan = document.getElementById('messages-total-count');
        
        try {
            const response = await fetch(`?delete=${msgId}&csrf=${csrfToken}&ajax=1`);
            if (!response.ok) throw new Error('Erreur réseau');
            
            const result = await response.json();
            if (result.success) {
                // Retirer la ligne avec une micro-animation de transition fluide
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.remove();
                    
                    // Mettre à jour le compteur de messages restants
                    const remainingRows = document.querySelectorAll('.message-row');
                    if (totalCountSpan) {
                        totalCountSpan.textContent = remainingRows.length;
                    }
                    
                    // Si aucun message ne reste, afficher l'état vide
                    if (remainingRows.length === 0) {
                        window.location.reload();
                    }
                }, 300);
                
                showToast('Supprimé ! Message effacé.', 'error');
            } else {
                showToast(result.message || 'Erreur lors de la suppression.', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Une erreur réseau est survenue.', 'error');
        }
    }
    </script>
</body>
</html>

