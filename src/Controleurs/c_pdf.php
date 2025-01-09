<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
var_dump($action);


// Vérifier si l'utilisateur est un comptable
if (!Utilitaires::estConnecteComptable()) {
    die('Erreur : Accès réservé aux comptables.');
}

switch ($action) {
    case 'telechargerPdf':


        // Récupérer les informations nécessaires (par exemple, visiteur et mois)
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        // Récupérer les données nécessaires pour le PDF (éléments forfaitisés, hors forfait, etc.)
        $elementsForfaitises = $pdo->getCopieFraisForfait($idVisiteur, $mois);
        $elementsHorsForfait = $pdo->getElementsHorsForfait($idVisiteur, $mois);

        // Appeler la vue pour générer le PDF
        include PATH_VIEWS . 'v_pdf.php';
        break;
}
