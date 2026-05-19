<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

// Action: Approuver/Rejeter
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $status = ($action === 'approve') ? 'Approuvée' : 'Rejetée';
    
    if ($action === 'approve') {
        // Generate Member ID (DSM-WH-YYYY-XXXX)
        $year = date('Y');
        // Count approved for this year
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM adhesions WHERE status = 'Approuvée' AND file_number LIKE ?");
        $count_stmt->execute(["DSM-WH-$year-%"]);
        $count = $count_stmt->fetchColumn() + 1;
        $file_number = "DSM-WH-$year-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        $member_uuid = bin2hex(random_bytes(16)); // Simple native UUID
        
        $stmt = $pdo->prepare("UPDATE adhesions SET status = :status, decision_by = :by, decision_date = NOW(), file_number = :file_number, member_uuid = :uuid WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':by' => $_SESSION['admin_id'],
            ':file_number' => $file_number,
            ':uuid' => $member_uuid,
            ':id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE adhesions SET status = :status, decision_by = :by, decision_date = NOW() WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':by' => $_SESSION['admin_id'],
            ':id' => $id
        ]);
    }
    
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => ($action === 'approve') ? 'La demande d\'adhésion a été approuvée avec succès.' : 'La demande d\'adhésion a été rejetée.',
            'action' => $action
        ]);
        exit;
    }
    
    $msg = ($action === 'approve') ? 'approved' : 'rejected';
    header('Location: ' . SITE_URL . '/admin/adhesions?msg=' . $msg);
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$status_filter = $_GET['status'] ?? 'En attente';

// Security check for Brouillon status
if ($status_filter === 'Brouillon' && ($_SESSION['admin_role'] ?? '') !== 'Superadmin') {
    $status_filter = 'En attente';
}

$where = "status = :status";

// Total
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM adhesions WHERE $where");
$count_stmt->execute([':status' => $status_filter]);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $per_page);

$adhesions = $pdo->prepare("SELECT * FROM adhesions WHERE $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$adhesions->bindValue(':status', $status_filter);
$adhesions->bindValue(':limit', $per_page, PDO::PARAM_INT);
$adhesions->bindValue(':offset', $offset, PDO::PARAM_INT);
$adhesions->execute();
$list = $adhesions->fetchAll();

$current_page = 'adhesions';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Adhésions - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        <header class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50 transition-all">
            <div>
                <h1 class="text-xl lg:text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Gestion des <span class="text-emerald-600">Adhésions</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Membres & Demandes</p>
            </div>
            <div id="total-badge" class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">
                <span id="adhesions-total-count"><?php echo $total_items; ?></span> Demandes
            </div>
        </header>

        <div class="p-8 space-y-8 animate-fade-in">
            <div class="flex items-center gap-4 bg-white dark:bg-slate-900 p-2 rounded-3xl border border-slate-200 dark:border-slate-800 w-fit">
                <a href="?status=En attente" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'En attente' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    En Attente
                </a>
                <a href="?status=Approuvée" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'Approuvée' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    Approuvées
                </a>
                <a href="?status=Rejetée" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'Rejetée' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                    Rejetées
                </a>
                <?php if(($_SESSION['admin_role'] ?? '') === 'Superadmin'): ?>
                    <a href="?status=Brouillon" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo $status_filter === 'Brouillon' ? 'bg-slate-600 text-white shadow-lg shadow-slate-600/20' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>">
                        Brouillons
                    </a>
                <?php endif; ?>
            </div>

            <div class="glass-panel rounded-[3rem] shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Membre</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date Demande</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        <?php foreach($list as $item): ?>
                        <tr id="adhesion-row-<?php echo $item['id']; ?>" class="hover:bg-emerald-50/10 dark:hover:bg-emerald-900/5 transition-all duration-300 group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200 dark:border-slate-700">
                                        <?php if($item['photo']): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/adhesions/<?php echo $item['photo']; ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-extrabold text-slate-900 dark:text-white uppercase tracking-tighter"><?php echo $item['last_name'] . ' ' . $item['first_name']; ?></span>
                                        <span class="text-[10px] font-bold text-slate-400 italic"><?php echo $item['profession']; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black px-3 py-1 rounded-full border <?php 
                                    echo $item['member_category'] === 'Membre d\'honneur' ? 'bg-amber-50 text-amber-600 border-amber-200' : 
                                        ($item['member_category'] === 'Membre effectif' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-slate-50 text-slate-600 border-slate-200'); 
                                ?> uppercase tracking-widest">
                                    <?php echo $item['member_category']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col text-xs font-bold text-slate-600 dark:text-slate-400">
                                    <span><?php echo $item['phone1']; ?></span>
                                    <span class="text-[9px] opacity-70"><?php echo $item['email']; ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-slate-400 uppercase tracking-widest">
                                <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="adhesion-detail?id=<?php echo $item['id']; ?>" class="w-11 h-11 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-lg border border-blue-500/10" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <?php if($status_filter === 'En attente'): ?>
                                        <a href="adhesions?action=approve&id=<?php echo $item['id']; ?>" 
                                           onclick="event.preventDefault(); showConfirm('Voulez-vous vraiment approuver cette demande d\'adhésion ?', () => processAdhesionAJAX(<?php echo $item['id']; ?>, 'approve'), 'success')" 
                                           class="w-11 h-11 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-lg border border-emerald-500/10" title="Approuver">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </a>
                                        <a href="adhesions?action=reject&id=<?php echo $item['id']; ?>" 
                                           onclick="event.preventDefault(); showConfirm('Rejeter cette demande d\'adhésion ?', () => processAdhesionAJAX(<?php echo $item['id']; ?>, 'reject'), 'danger')" 
                                           class="w-11 h-11 bg-rose-50 dark:bg-rose-900/20 text-rose-600 rounded-2xl flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-lg border border-rose-500/10" title="Rejeter">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($list)): ?>
                            <tr><td colspan="5" class="p-20 text-center text-slate-400 italic font-bold">Aucune demande dans cette catégorie.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <!-- Pagination UI (Similaire aux autres pages) -->
            <?php endif; ?>
        </div>
    </main>

    <!-- AJAX JS Script Integration -->
    <script>
    async function processAdhesionAJAX(id, action) {
        const row = document.getElementById(`adhesion-row-${id}`);
        const totalCountSpan = document.getElementById('adhesions-total-count');
        
        try {
            const response = await fetch(`adhesions?action=${action}&id=${id}&ajax=1`);
            if (!response.ok) throw new Error('Erreur réseau');
            
            const result = await response.json();
            if (result.success) {
                // Micro-animation smooth exit
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.remove();
                    
                    // Update remaining badge counter
                    const remainingRows = document.querySelectorAll('tbody tr[id^="adhesion-row-"]');
                    if (totalCountSpan) {
                        totalCountSpan.textContent = remainingRows.length;
                    }
                    
                    // Reload if table is empty to show empty state correctly
                    if (remainingRows.length === 0) {
                        window.location.reload();
                    }
                }, 300);
                
                showToast(result.message, action === 'approve' ? 'success' : 'error');
            } else {
                showToast(result.message || 'Une erreur est survenue.', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Une erreur réseau est survenue.', 'error');
        }
    }
    </script>
</body>
</html>
