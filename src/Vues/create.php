<?php
//require('fpdf186/fpdf.php');
require('fpdf186/mc_table.php');

$pdfName = $_POST['pdfName'];
$pdfAddress = $_POST['pdfAddress'];
$pdfAddress2 = $_POST['pdfAddress2'];
$pdfCity = $_POST['pdfCity'];
$pdfState = $_POST['pdfState'];
$pdfZip = $_POST['pdfZip'];
$pdfCountry = $_POST['pdfCountry'];
$pdfPhone = $_POST['pdfPhone'];
$pdfEmail = $_POST['pdfEmail'];


// $pdf = new FPDF('P','mm','A4');
$pdf = new PDF_MC_Table('P','mm','A4');
// A4 = 210mm × 297mm

$pdf->SetTitle('FPDF Tutorial');
$pdf->SetAutoPageBreak(true, 10);

$pdf->AddPage();

$pdf->SetFont('Arial','BU',16);
$pdf->Cell(190 ,5,'FPDF Tutorial', 0, 1, "C");

$pdf->Image('favicon.png',20,25,-400);

$pdf->Ln(40);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(18, 5,"Name:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(40, 5, $pdfName, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(25, 5,"Address:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(80, 5, $pdfAddress, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(28, 5,"Address2:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(60, 5, $pdfAddress2, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(13, 5,"City:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(60, 5, $pdfCity, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(16, 5,"State:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(60, 5, $pdfState, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(11, 5,"Zip:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(40, 5, $pdfZip, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(24, 5,"Country:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(60, 5, $pdfCountry, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(20, 5,"Phone:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(60, 5, $pdfPhone, 0, "L");

$pdf->Ln(4);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(18, 5,"Email:", 0, 0);
$pdf->SetFont('Arial', '', 16);
$pdf->MultiCell(80, 5, $pdfEmail, 0, "L");

$pdf->Ln(15);

$pdf->SetWidths(array(12,71,25,40,42));
for($i = 1; $i <= 40; $i++) {
    $pdf->Row(array(1, 2, 3, 4, 5));
}

$pdf->Output('I', 'Test.pdf');
?>