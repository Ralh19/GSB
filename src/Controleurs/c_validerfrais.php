<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!Utilitaires::estConnecteComptable()) {
    die('Erreur : Accès réservé aux comptables.');
}

switch ($action) {
    case 'validerFrais':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getMoisDisponibles();

        if (isset($_POST['lstVisiteur']) && isset($_POST['lstMois'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        }


        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

}