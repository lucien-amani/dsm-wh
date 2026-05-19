<?php
require_once '../config/database.php';
require_once 'includes/auth.php';
require_once '../includes/fpdf_minimal.php';

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

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header
$pdf->Cell(190, 10, 'DYNAMIQUE SAMY MAGADJU - DSM-WH', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, 'Wema ni Hakiba - A.S.B.L.', 0, 1, 'C');
$pdf->Cell(190, 10, 'FICHE D\'ADHESION OFFICIELLE', 0, 1, 'C');
$pdf->Ln(5);

// Member Info
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'IDENTITE DU MEMBRE', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, 'Nom: ' . $item['last_name'], 1, 0);
$pdf->Cell(95, 8, 'Post-nom: ' . $item['post_name'], 1, 1);
$pdf->Cell(95, 8, 'Prenom: ' . $item['first_name'], 1, 0);
$pdf->Cell(95, 8, 'Nationalite: ' . $item['nationality'], 1, 1);
$pdf->Cell(190, 8, 'Lieu et Date de Naissance: ' . $item['birth_place'] . ', ' . $item['birth_date'], 1, 1);
$pdf->Cell(95, 8, 'Etat Civil: ' . $item['civil_status'], 1, 0);
$pdf->Cell(95, 8, 'Profession: ' . $item['profession'], 1, 1);
$pdf->Ln(5);

// Address
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'COORDONNEES', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 8, 'Adresse: ' . $item['address_details'], 1, 1);
$pdf->Cell(95, 8, 'Province: ' . $item['province'], 1, 0);
$pdf->Cell(95, 8, 'Ville/District: ' . $item['city_district'], 1, 1);
$pdf->Cell(95, 8, 'Telephone: ' . $item['phone1'], 1, 0);
$pdf->Cell(95, 8, 'Email: ' . $item['email'], 1, 1);
$pdf->Ln(5);

// Status
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'STATUT ET ENGAGEMENT', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, 'Categorie: ' . ($item['member_category'] ?? 'Non definie'), 1, 0);
$pdf->Cell(95, 8, 'UUID: ' . ($item['member_uuid'] ?? 'NON ASSIGNE'), 1, 1);
$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(190, 10, 'Document genere automatiquement par le portail DSM-WH. Fait le ' . date('d/m/Y'), 0, 1, 'C');

$filename = 'Fiche_Adhesion_' . $item['last_name'] . '.pdf';
$pdf->Output($filename, 'D');
