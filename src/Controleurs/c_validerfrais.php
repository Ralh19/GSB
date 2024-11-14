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
        $infosVisiteur = []; // Initialiser infosVisiteur comme un tableau vide
        // Si un visiteur a été sélectionné
        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);

            // Sauvegarder l'ID du visiteur dans la session
            $_SESSION['idVisiteur'] = $idVisiteur;

            // Récupérer les mois associés à ce visiteur où l'état est CR ou CL
            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

            // Récupérer les informations du visiteur pour l'affichage
            $infosVisiteur = $pdo->getVisiteurInfo($idVisiteur);

            // Enregistrer le nom et le prénom du visiteur dans la session
            $_SESSION['nomVisiteur'] = $infosVisiteur['nom'];
            $_SESSION['prenomVisiteur'] = $infosVisiteur['prenom'];
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
