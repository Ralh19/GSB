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
        $moisASelectionner = ''; // Initialiser la variable du mois sélectionné
        // Si un visiteur a été sélectionné
        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            // Récupérer les mois associés à ce visiteur où l'état est CR ou CL
            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
            $_SESSION['idVisiteur'] = $idVisiteur; // Sauvegarder l'ID du visiteur dans la session
        }

        // Si un mois a été sélectionné
        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            $moisASelectionner = $mois; // Enregistrer le mois sélectionné pour l'afficher
            // Récupérer les éléments forfaitisés pour ce mois et ce visiteur
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteur'], $mois);
        }

        // Passer les données à la vue pour l'affichage
        include PATH_VIEWS . 'v_valider_fiche_frais.php'; // Inclure la vue pour afficher le formulaire
        break;
}
