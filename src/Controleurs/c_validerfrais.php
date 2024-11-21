<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!Utilitaires::estConnecteComptable()) {
    die('Erreur : Accès réservé aux comptables.');
}

switch ($action) {
    case 'validerFrais':

        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = [];
        $ficheFrais = [];
        $elementsForfaitises = [];
        $elementsHorsForfait = [];
        $moisASelectionner = '';
        $infosVisiteur = [];

        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            $_SESSION['idVisiteur'] = $idVisiteur;

            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

            $infosVisiteur = $pdo->getVisiteurInfo($idVisiteur);

            $_SESSION['nomVisiteur'] = $infosVisiteur['nom'];
            $_SESSION['prenomVisiteur'] = $infosVisiteur['prenom'];
        }


        // Si un mois a été sélectionné
        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            $moisASelectionner = $mois; 
            $resultatsFicheFrais = $pdo->getLesInfosFicheFrais2($_SESSION['idVisiteur'], $mois);
            $ficheFrais = $resultatsFicheFrais['ficheFrais'];
            $elementsForfaitises = $resultatsFicheFrais['elementsForfaitises'];

            $elementsHorsForfait = $pdo->getElementsHorsForfait($_SESSION['idVisiteur'], $mois);
        }




        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;
}
?>
