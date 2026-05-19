<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();

if (!isset($_GET['id'])) {
    die("ID manquant.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.full_name as decision_by_name FROM adhesions a LEFT JOIN users u ON a.decision_by = u.id WHERE a.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("Demande introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche_Adhesion_<?php echo $item['last_name']; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: white; }
        @media print {
            @page { 
                margin: 0; 
                size: A4 portrait;
            }
            body { 
                margin: 0; 
                padding: 15mm;
            }
            .no-print { display: none !important; }
        }
        .fiche-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            background: white;
        }
    </style>
</head>
<body class="bg-slate-500/20">

    <div class="no-print bg-slate-900 text-white p-4 flex justify-between items-center sticky top-0 z-50 backdrop-blur-md bg-opacity-80">
        <div class="flex items-center gap-4">
            <a href="adhesion-detail?id=<?php echo $item['id']; ?>" class="text-xs font-black uppercase tracking-widest hover:text-emerald-400 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <span class="h-4 w-px bg-slate-700"></span>
            <h1 class="text-xs font-black uppercase tracking-[0.2em]">Prévisualisation de la Fiche</h1>
        </div>
        <div class="flex gap-4">
            <a href="adhesion-download?id=<?php echo $item['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Télécharger (Fichier PDF Pur)
            </a>
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                Imprimer / PDF (Navigateur)
            </button>
        </div>
    </div>

    <div class="fiche-container shadow-2xl my-10 print:my-0 print:border-none">
        <!-- Header -->
        <div class="p-8 border-b-4 border-emerald-600 flex justify-between items-center bg-slate-50">
            <div class="flex items-center gap-4">
                <img src="<?php echo SITE_URL; ?>/assets/logo/logo-dsm.jpg" class="h-20 w-20 rounded-full border-2 border-emerald-600">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 uppercase leading-tight">Dynamique Samy Magadju</h2>
                    <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest">Wema ni Hakiba - A.S.B.L.</p>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase">Fiche d'Adhésion Officielle</p>
                </div>
            </div>
            <div class="text-right">
                <div class="border-2 border-emerald-600/20 p-4 rounded-lg">
                    <p class="text-[10px] uppercase font-bold text-gray-400">N° d'Enregistrement</p>
                    <p class="text-xl font-mono font-black text-emerald-600"><?php echo $item['file_number'] ?: 'Draft-' . $item['id']; ?></p>
                </div>
            </div>
        </div>

        <div class="p-10 space-y-12">
            <!-- Photo & Identity -->
            <div class="flex gap-10">
                <div class="w-44 h-56 border-2 border-slate-200 bg-slate-50 flex-shrink-0">
                    <?php if($item['photo']): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/adhesions/<?php echo $item['photo']; ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1 grid grid-cols-2 gap-x-8 gap-y-4">
                    <div class="col-span-2 border-b border-slate-100 pb-1">
                        <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Identité du Membre</h3>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Nom Complet</p>
                        <p class="text-sm font-bold text-slate-900 uppercase"><?php echo $item['last_name'] . ' ' . $item['post_name'] . ' ' . $item['first_name']; ?></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Nationalité</p>
                        <p class="text-sm font-bold text-slate-900"><?php echo $item['nationality']; ?></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Lieu & Date de Naissance</p>
                        <p class="text-sm font-bold text-slate-900 uppercase"><?php echo $item['birth_place'] . ', le ' . date('d/m/Y', strtotime($item['birth_date'])); ?></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">État Civil</p>
                        <p class="text-sm font-bold text-slate-900 uppercase"><?php echo $item['civil_status']; ?> (<?php echo $item['children_count']; ?> enfants)</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[8px] font-black text-slate-400 uppercase">Profession</p>
                        <p class="text-sm font-bold text-slate-900 uppercase"><?php echo $item['profession']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Address & Contact -->
            <div class="grid grid-cols-2 gap-10">
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest border-b border-slate-100 pb-1">Adresse Résidentielle</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase">Province</p>
                            <p class="text-xs font-bold text-slate-700 uppercase"><?php echo $item['province']; ?></p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase">Ville / District</p>
                            <p class="text-xs font-bold text-slate-700 uppercase"><?php echo $item['city_district']; ?></p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Adresse Détaillée</p>
                        <p class="text-xs font-bold text-slate-700 uppercase italic"><?php echo $item['address_details']; ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest border-b border-slate-100 pb-1">Contacts</h3>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Téléphones</p>
                        <p class="text-xs font-bold text-slate-700"><?php echo $item['phone1']; ?> <?php echo $item['phone2'] ? ' / ' . $item['phone2'] : ''; ?></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Email</p>
                        <p class="text-xs font-bold text-slate-700"><?php echo $item['email'] ?: '---'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Engagement -->
            <div class="p-6 bg-slate-50 rounded-2xl flex justify-between items-center border border-slate-100">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Statut Adhésion</p>
                    <p class="text-lg font-black text-slate-900 uppercase"><?php echo $item['member_category']; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Frais Versés</p>
                    <p class="text-lg font-black text-emerald-600"><?php echo $item['membership_fee'] ?: '0.00 USD'; ?></p>
                </div>
            </div>

            <!-- Approval -->
            <div class="grid grid-cols-2 gap-12 pt-10 border-t border-slate-100">
                <div class="space-y-6">
                    <h4 class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Authentification du Document</h4>
                    <div class="flex items-start gap-4">
                        <?php 
                        $uuid = $item['member_uuid'] ?? '';
                        $qr_url = SITE_URL . '/verify?uuid=' . $uuid;
                        // Use qrserver api as fallback/alternative to google charts
                        $qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_url);
                        ?>
                        <img src="<?php echo $qr_image_url; ?>" alt="QR Code" class="w-24 h-24 border border-slate-100 p-1 bg-white">
                        <div class="space-y-2">
                            <p class="text-[7px] text-slate-400 font-bold uppercase leading-tight">Ce document est protégé par un identifiant unique (UUID). Scannez le code pour vérifier l'authenticité.</p>
                            <p class="text-[8px] font-mono font-black text-slate-900 break-all"><?php echo $uuid ?: 'NON ASSIGNÉ'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="text-right flex flex-col justify-end">
                    <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-8">Le Secrétaire Général</p>
                    <div class="w-48 h-px bg-slate-900 ml-auto"></div>
                    <p class="text-[7px] text-slate-300 mt-1 uppercase italic">(Sceau et Signature)</p>
                </div>
            </div>
        </div>

        <div class="p-6 text-center text-[7px] text-slate-300 uppercase tracking-[0.4em] border-t border-slate-50">
            Document Généré par DSM-WH Portal - <?php echo date('d/m/Y H:i'); ?>
        </div>
    </div>

</body>
</html>
