<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!Utilitaires::estConnecteComptable()) {
    die('Erreur : Accès réservé aux comptables.');
}

switch ($action) {
    case 'validerfrais':
        $lesVisiteurs = $pdo->getListeVisiteurs();
        $lesMois = $pdo->getTousLesMois();

        if (isset($_POST['lstVisiteur']) && isset($_POST['lstMois'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        }


        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

}