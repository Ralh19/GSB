<?php
ob_clean();
require_once __DIR__ . '/../../vendor/fpdf/fpdf.php';
require_once __DIR__ . '/../Modeles/PdoGsb.php';

use Modeles\PdoGsb;

// Sécurisation des paramètres GET
$idVisiteur = $_GET['id'] ?? '';
$mois = $_GET['mois'] ?? '';
$moisFormate = substr($mois, 4, 2) . '/' . substr($mois, 0, 4); // Donne "09/2023"


if (!$idVisiteur || !$mois) {
    http_response_code(400);
    exit('Paramètres manquants.');
}

// Récupération des données
$pdo = PdoGsb::getPdoGsb();
$infosIdentite = $pdo->getNomPrenomVisiteurParId($idVisiteur);
$infosFiche = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
$fraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
$fraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);

$montants = $pdo->getMontantsFraisForfait();
$montantEtape = $montants['ETP'] ?? 0;
$montantNuitee = $montants['NUI'] ?? 0;
$montantRepas = $montants['REP'] ?? 0;

$montantKm = 0;
$quantiteNuitee = 0;
$quantiteRepas = 0;
$quantiteEtape = 0;
$quantiteKm = 0;

$idsKm = $pdo->getIdsFraisKilometriques();

// Attribution des quantités
foreach ($fraisForfait as $frais) {
    $id = $frais['idfrais'];
    $quantite = $frais['quantite'];

    if (in_array($id, $idsKm)) {
        $quantiteKm = $quantite;
        $montantKm = $montants[$id] ?? 0;
    } elseif ($id === 'NUI') {
        $quantiteNuitee = $quantite;
    } elseif ($id === 'REP') {
        $quantiteRepas = $quantite;
    } elseif ($id === 'ETP') {
        $quantiteEtape = $quantite;
    }
}
$totalFraisForfait = 
    $quantiteNuitee * $montantNuitee +
    $quantiteRepas * $montantRepas +
    $quantiteKm * $montantKm;

$totalFraisHorsForfait = 0;
foreach ($fraisHorsForfait as $frais) {
    $totalFraisHorsForfait += $frais['montant'];
}


$totalGlobal = $totalFraisForfait + $totalFraisHorsForfait;

// Exemple : $mois = "202309";
$date = DateTime::createFromFormat('Ym', $mois);

// Tableau des mois en français
$moisFrancais = [
    '01' => 'Janvier',
    '02' => 'Février',
    '03' => 'Mars',
    '04' => 'Avril',
    '05' => 'Mai',
    '06' => 'Juin',
    '07' => 'Juillet',
    '08' => 'Août',
    '09' => 'Septembre',
    '10' => 'Octobre',
    '11' => 'Novembre',
    '12' => 'Décembre'
];

// Récupération du mois et de l'année
$numMois = $date->format('m');
$annee = $date->format('Y');

// Construction du résultat
$moisFR = $moisFrancais[$numMois] . ' ' . $annee;


// Préparation du PDF
if (ob_get_length()) ob_end_clean();

$pdf = new FPDF();
$pdf->AddPage();

// Titre
$imageWidth = 40; // largeur que tu veux pour ton image
$pageWidth = $pdf->GetPageWidth();
$x = ($pageWidth - $imageWidth) / 2;
$pdf->Image(__DIR__ . '/../../public/images/logo.jpg', $x, 10, $imageWidth);
$pdf->Ln(40);

// Entête
// Largeur totale du tableau (par ex. 170 mm)
$largeurTotale = 170;
// Calcul du X pour centrer le tableau
$pdf->SetX(($pdf->GetPageWidth() - $largeurTotale) / 2);
$pdf->SetFont('Arial', 'B', 13.5);
// Cellule titre : largeur du tableau complet, hauteur, texte, bordure=1, saut de ligne=1, alignement=C
$pdf->Cell($largeurTotale, 10, iconv('UTF-8', 'ISO-8859-1', 'REMBOURSEMENT DE FRAIS ENGAGES'), 1, 1, 'C');


// Ligne 1
// Définir les largeurs de chaque colonne (elles doivent s’additionner à la largeur du tableau)
$largeurs = [10, 50, 40, 60, 10]; // Total = 170 mm
// Repositionnement horizontal pour centrer
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Créer la ligne vide avec 5 colonnes
foreach ($largeurs as $w) {
    $pdf->Cell($w, 8, '', 1); // contenu vide, hauteur 8mm, bordure visible
}
$pdf->Ln(); // Passe à la ligne suivante

// Ligne 2
$largeurs = [10, 50, 40, 60, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Visiteur'), 1);
$pdf->Cell($largeurs[2], 8, iconv('UTF-8', 'ISO-8859-1', $idVisiteur), 1);
$pdf->Cell($largeurs[3], 8, iconv('UTF-8', 'ISO-8859-1', $infosIdentite['prenom'] . ' ' . $infosIdentite['nom']), 1);
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 3
$largeurs = [10, 50, 40, 60, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Mois'), 1);
$pdf->Cell($largeurs[2], 8, iconv('UTF-8', 'ISO-8859-1', $moisFR), 1);
$pdf->Cell($largeurs[3], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 4
$largeurs = [10, 50, 40, 60, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[3], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 5
$largeurs = [10, 50, 40, 35, 25, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', 'BI', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Frais Forfaitaires'), 1, 0, 'C');
$pdf->Cell($largeurs[2], 8, iconv('UTF-8', 'ISO-8859-1', 'Quantité'), 1, 0, 'C');
$pdf->Cell($largeurs[3], 8, iconv('UTF-8', 'ISO-8859-1', 'Montant unitaire'), 1, 0, 'C');
$pdf->Cell($largeurs[4], 8, iconv('UTF-8', 'ISO-8859-1', 'Total'), 1, 0, 'C');
$pdf->Cell($largeurs[5], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 6
$largeurs = [10, 50, 40, 35, 25, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Nuitée'), 1);
$pdf->Cell($largeurs[2], 8, $quantiteNuitee, 1, 0, 'R');
$pdf->Cell($largeurs[3], 8, number_format($montantNuitee, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[4], 8, number_format($quantiteNuitee * $montantNuitee, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[5], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 7
$largeurs = [10, 50, 40, 35, 25, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Repas Midi'), 1);
$pdf->Cell($largeurs[2], 8, $quantiteRepas, 1, 0, 'R');
$pdf->Cell($largeurs[3], 8, number_format($montantRepas, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[4], 8, number_format($quantiteRepas * $montantRepas, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[5], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 8
$largeurs = [10, 50, 40, 35, 25, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Véhicule'), 1);
$pdf->Cell($largeurs[2], 8, $quantiteKm, 1, 0, 'R');
$pdf->Cell($largeurs[3], 8, number_format($montantKm, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[4], 8, number_format($quantiteKm * $montantKm, 2, '.', ''), 1, 0, 'R');
$pdf->Cell($largeurs[5], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 9
$largeurs = [10, 50, 40, 60, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[3], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 10
$largeurs = [10, 150, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', 'BI', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Autres Frais'), 1, 0, 'C');
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 11
$largeurs = [10, 50, 75, 25, 10];

$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', 'BI', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, iconv('UTF-8', 'ISO-8859-1', 'Date'), 1, 0, 'C');
$pdf->Cell($largeurs[2], 8, iconv('UTF-8', 'ISO-8859-1', 'Libellé'), 1, 0, 'C');
$pdf->Cell($largeurs[3], 8, iconv('UTF-8', 'ISO-8859-1', 'Montant'), 1, 0, 'C');
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 12
$pdf->SetFont('Arial', '', 11); // Texte normal pour les lignes
foreach ($fraisHorsForfait as $frais) {
    $date = $frais['date'];
    $libelle = iconv('UTF-8', 'ISO-8859-1', $frais['libelle']);
    $montant = number_format($frais['montant'], 2, ',', ' ');

    $pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
    $pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
    $pdf->Cell($largeurs[1], 8, $date, 1);
    $pdf->Cell($largeurs[2], 8, $libelle, 1);
    $pdf->Cell($largeurs[3], 8, $montant, 1, 0, 'R');
    $pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
    $pdf->Ln();
}

// Ligne 13
$largeurs = [10, 150, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', 'BI', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 14
$largeurs = [10, 50, 40, 35, 25, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[3], 8, iconv('UTF-8', 'ISO-8859-1', 'TOTAL ' . $moisFormate), 1);
$pdf->Cell($largeurs[4], 8, iconv('UTF-8', 'ISO-8859-1', $totalGlobal), 1, 0, 'R');
$pdf->Cell($largeurs[5], 8, '', 1); // Colonne vide
$pdf->Ln();

// Ligne 15
$largeurs = [10, 50, 40, 60, 10];
// Centrer horizontalement le tableau (170mm de large)
$pdf->SetX(($pdf->GetPageWidth() - array_sum($largeurs)) / 2);
// Contenu de la ligne
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($largeurs[0], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[1], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[2], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[3], 8, '', 1); // Colonne vide
$pdf->Cell($largeurs[4], 8, '', 1); // Colonne vide
$pdf->Ln();

$aujourdhui = new DateTime();
$jour = $aujourdhui->format('j'); // sans zéro initial
$mois = $moisFrancais[$aujourdhui->format('m')];
$annee = $aujourdhui->format('Y');

$dateFormatee = "Fait à Paris, le $jour $mois $annee";

$pdf->Ln(30); // un peu d’espace après le tableau
$pdf->SetFont('Arial', '', 12);
// Positionné vers la droite (ajuste X si besoin)
$pdf->SetX(110);
$pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1', $dateFormatee), 0, 1, 'L');

$pdf->SetX(110);
$pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1', "Vu l'agent comptable"), 0, 1, 'L');
$signaturePath = __DIR__ . '/../../public/images/signature.png';
if (file_exists($signaturePath)) {
    $pdf->Image($signaturePath, 100, $pdf->GetY()+5, 70); // x, y, largeur (ajuste si besoin)
}

// Export
$mois = $_GET['mois'] ?? '';
$filename = __DIR__ . '/../../exports/fiche_frais_' . $mois . '_' . $idVisiteur . '.pdf';

if (file_exists($filename)) {
    // Sert le PDF déjà généré
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    exit;
} else {
    // Génère le fichier et le sert immédiatement
    $pdf->Output('F', $filename); // Sauvegarde
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    exit;
}
