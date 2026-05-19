<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

if (!isset($_GET['id'])) {
    header('Location: ' . SITE_URL . '/admin/adhesions');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.full_name as decision_by_name FROM adhesions a LEFT JOIN users u ON a.decision_by = u.id WHERE a.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("Demande introuvable.");
}

$current_page = 'adhesions';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Adhésion - <?php echo $item['last_name']; ?> - DSM ADMIN</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        @media print {
            @page { size: A4; margin: 0; }
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .print-container { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                height: 100vh;
            }
            .print-header { background: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .bg-emerald-600 { background-color: #059669 !important; -webkit-print-color-adjust: exact; }
            .text-emerald-600 { color: #059669 !important; -webkit-print-color-adjust: exact; }
            .bg-slate-50 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0 min-h-screen scroll-smooth">
        <header class="h-20 lg:h-24 flex items-center justify-between px-4 lg:px-12 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50 transition-all no-print">
            <div class="flex items-center gap-4">
                <a href="adhesions" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-500 hover:bg-emerald-600 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-xl lg:text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Détails <span class="text-emerald-600">Adhésion</span></h1>
            </div>
            <div class="flex gap-3 no-print">
                <a href="adhesion-export?id=<?php echo $item['id']; ?>" target="_blank" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Télécharger la Fiche (PDF)
                </a>
            </div>
        </header>

        <div class="p-8 lg:p-12 animate-fade-in">
            <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-800 print-container overflow-hidden">
                
                <!-- En-tête identique au formulaire -->
                <div class="p-8 border-b-4 border-emerald-600 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="flex items-center gap-4">
                        <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" class="h-20 w-20 rounded-full border-2 border-emerald-600">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase leading-tight">Dynamique Samy Magadju</h2>
                            <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest">Wema ni Hakiba - A.S.B.L.</p>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase">Fiche d'Adhésion Officielle</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="border-2 border-emerald-600/20 p-4 rounded-lg">
                            <p class="text-[10px] uppercase font-bold text-gray-400">N° d'Enregistrement</p>
                            <p class="text-xl font-mono font-black text-emerald-600"><?php echo $item['file_number'] ?: 'NON ATTRIBUÉ'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="p-10 space-y-12">
                    <!-- Photo & Identity Header -->
                    <div class="flex flex-col md:flex-row gap-10">
                        <div class="w-48 h-64 border-4 border-double border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
                            <?php if($item['photo']): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/adhesions/<?php echo $item['photo']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-[8px] font-black uppercase mt-2 text-slate-400">AUCUNE PHOTO</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="col-span-2 border-b border-slate-100 dark:border-slate-800 pb-2 mb-2">
                                <h3 class="text-xs font-black text-emerald-600 uppercase tracking-widest">État Civil & Identité</h3>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Nom Complet</p>
                                <p class="font-bold text-slate-900 dark:text-white uppercase"><?php echo $item['last_name'] . ' ' . $item['post_name'] . ' ' . $item['first_name']; ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Nationalité</p>
                                <p class="font-bold text-slate-900 dark:text-white"><?php echo $item['nationality']; ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Lieu & Date de Naissance</p>
                                <p class="font-bold text-slate-900 dark:text-white uppercase"><?php echo $item['birth_place'] . ', le ' . date('d/m/Y', strtotime($item['birth_date'])); ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase">État Civil</p>
                                <p class="font-bold text-slate-900 dark:text-white uppercase"><?php echo $item['civil_status']; ?> (<?php echo $item['children_count']; ?> enfants)</p>
                            </div>
                            <div class="col-span-2 space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Profession</p>
                                <p class="font-bold text-slate-900 dark:text-white uppercase"><?php echo $item['profession']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Contact -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 border-t border-slate-100 dark:border-slate-800 pt-10">
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-emerald-600 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-2">Ancrage Géographique</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Province</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm"><?php echo $item['province']; ?></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Ville / District</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm"><?php echo $item['city_district']; ?></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Commune / Territoire</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm"><?php echo $item['commune_territory']; ?></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase">Quartier / Secteur</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm"><?php echo $item['quarter_secteur']; ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase">Adresse Détaillée</p>
                                <p class="font-bold text-slate-700 dark:text-slate-300 uppercase text-sm italic"><?php echo $item['address_details']; ?></p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-emerald-600 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-2">Coordonnées & Contact</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase">Téléphones</p>
                                        <p class="font-bold text-slate-700 dark:text-slate-300"><?php echo $item['phone1']; ?> <?php echo $item['phone2'] ? ' / ' . $item['phone2'] : ''; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase">Adresse E-mail</p>
                                        <p class="font-bold text-slate-700 dark:text-slate-300 lowercase"><?php echo $item['email'] ?: 'Non fournie'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Category & Commitment -->
                    <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-3xl flex flex-col md:flex-row justify-between items-center gap-8">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center text-emerald-600 shadow-xl shadow-emerald-600/10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie de Membre</p>
                                <p class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tighter"><?php echo $item['member_category']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Frais versés</p>
                            <p class="text-xl font-black text-emerald-600 tracking-tighter"><?php echo $item['membership_fee'] ?: '0.00 USD'; ?></p>
                        </div>
                    </div>

                    <!-- Administration Section (Approval) -->
                    <div class="mt-12 p-10 border-2 border-slate-100 dark:border-slate-800 rounded-[2.5rem] bg-white dark:bg-slate-900 relative overflow-hidden">
                        <?php if($item['status'] === 'Approuvée'): ?>
                            <div class="absolute -right-8 -top-8 w-40 h-40 bg-emerald-500/10 rounded-full flex items-center justify-center -rotate-12">
                                <span class="text-emerald-500 font-black uppercase text-xl border-4 border-emerald-500 p-4 rounded-xl">APPROUVÉE</span>
                            </div>
                        <?php elseif($item['status'] === 'Rejetée'): ?>
                            <div class="absolute -right-8 -top-8 w-40 h-40 bg-rose-500/10 rounded-full flex items-center justify-center -rotate-12">
                                <span class="text-rose-500 font-black uppercase text-xl border-4 border-rose-500 p-4 rounded-xl">REJETÉE</span>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div class="space-y-6">
                                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em]">Décision du Bureau</h4>
                                <div class="flex gap-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 border-2 <?php echo $item['status'] === 'Approuvée' ? 'bg-emerald-600 border-emerald-600' : 'border-slate-200'; ?> rounded-sm flex items-center justify-center">
                                            <?php if($item['status'] === 'Approuvée'): ?><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><?php endif; ?>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-widest <?php echo $item['status'] === 'Approuvée' ? 'text-emerald-600' : 'text-slate-300'; ?>">Demande Approuvée</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 border-2 <?php echo $item['status'] === 'Rejetée' ? 'bg-rose-600 border-rose-600' : 'border-slate-200'; ?> rounded-sm flex items-center justify-center">
                                            <?php if($item['status'] === 'Rejetée'): ?><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg><?php endif; ?>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-widest <?php echo $item['status'] === 'Rejetée' ? 'text-rose-600' : 'text-slate-300'; ?>">Demande Rejetée</span>
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-4">
                                    Traitée par : <span class="text-slate-900 dark:text-white"><?php echo $item['decision_by_name'] ?: 'En attente'; ?></span> 
                                    le <?php echo $item['decision_date'] ? date('d/m/Y H:i', strtotime($item['decision_date'])) : '--/--/----'; ?>
                                </div>
                            </div>
                            <div class="text-right flex flex-col justify-end items-end">
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Le Secrétaire Général / Administratif</p>
                                <div class="h-20 w-48 border-b-2 border-slate-100 dark:border-slate-800 flex items-center justify-center italic text-slate-300 text-[10px]">
                                    (Cachet et Signature)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Print Footer -->
                <div class="p-8 text-center text-[8px] text-slate-300 uppercase tracking-[0.5em] border-t border-slate-50 dark:border-slate-800/50">
                    Document Officiel DSM-WH - Toute reproduction sans autorisation est interdite
                </div>
            </div>

            <!-- Admin Actions (Sticky bottom bar) no-print -->
            <?php if($item['status'] === 'En attente'): ?>
            <div class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-white dark:bg-slate-900 shadow-2xl rounded-3xl border border-slate-200 dark:border-slate-800 p-4 flex gap-4 no-print z-50 animate-slide-up">
                <a href="adhesions?action=approve&id=<?php echo $item['id']; ?>" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-emerald-700 transition-all flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approuver la Demande
                </a>
                <a href="adhesions?action=reject&id=<?php echo $item['id']; ?>" class="bg-rose-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-rose-700 transition-all flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Rejeter la Demande
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
