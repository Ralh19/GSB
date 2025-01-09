<?php

require('./fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->setFont('Arial', "B", 17);
        $this->Cell(80);
        $this->Cell(30, 10, 'Fiche de Frais', 1, 0, 'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, "Page " . $this->PageNo());
    }

    function Body($elementsForfaitises, $elementsHorsForfait, $mois, $nomVisiteur, $prenomVisiteur)
    {
        // Titre du document
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Visiteur: ' . $nomVisiteur . ' ' . $prenomVisiteur, 0, 1);
        $this->Cell(0, 10, 'Mois: ' . substr($mois, 4, 2) . '/' . substr($mois, 0, 4), 0, 1);
        $this->Ln(10);

        // Éléments forfaitisés
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, 'Éléments forfaitisés:', 0, 1);
        $this->SetFont('Arial', '', 10);
        foreach ($elementsForfaitises as $element) {
            $this->Cell(0, 10, $element['libelle'] . ": " . $element['quantite'], 0, 1);
        }
        $this->Ln(10);

        // Éléments hors forfait
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, 'Éléments hors forfait:', 0, 1);
        $this->SetFont('Arial', '', 10);
        foreach ($elementsHorsForfait as $element) {
            $this->Cell(0, 10, $element['date'] . " - " . $element['libelle'] . ": " . $element['montant'] . '€', 0, 1);
        }
        $this->Ln(10);
    }
}

// Création du PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);

// Appeler la méthode Body avec les données dynamiques
$pdf->Body($elementsForfaitises, $elementsHorsForfait, $mois, $_SESSION['nomVisiteur'], $_SESSION['prenomVisiteur']);

// Sortie du PDF
$pdf->Output();
exit;
