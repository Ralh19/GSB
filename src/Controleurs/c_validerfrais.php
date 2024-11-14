<?php

use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!Utilitaires::estConnecteComptable()) {
    die('Erreur : Accès réservé aux comptables.');
}

switch ($action) {
    case 'validerFrais':
        // Récupérer les visiteurs depuis la base de données
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = [];
        $infosFicheFrais = [];

        // Si un visiteur a été sélectionné
        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            var_dump($idVisiteur); // Déboguer : Afficher l'ID du visiteur pour vérifier la récupération
            // Récupérer les mois associés à ce visiteur
            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        }

        // Si un mois a été sélectionné
        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

            // Récupérer les éléments forfaitisés pour ce mois et ce visiteur
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        }

        // Passer les données à la vue pour l'affichage
        include PATH_VIEWS . 'v_valider_fiche_frais.php'; // Inclure la vue pour afficher le formulaire
        break;
}