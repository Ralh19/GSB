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

        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            $_SESSION['moisSelectionne'] = $mois;
            $moisASelectionner = $mois;

            // Charger les frais pour ce mois
            $elementsForfaitises = $pdo->getCopieFraisForfait($_SESSION['idVisiteur'], $mois);
            $elementsHorsForfait = $pdo->getElementsHorsForfait($_SESSION['idVisiteur'], $mois);
        }

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

    case 'corrigerReinitialiserForfait':
        $actionForfait = filter_input(INPUT_POST, 'actionForfait', FILTER_SANITIZE_STRING);
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];
        $moisASelectionner = $mois; // Ajoutez cette ligne pour éviter l'erreur

        if ($actionForfait === 'corriger') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'quantite_') === 0) {
                    $idFraisForfait = str_replace('quantite_', '', $key);
                    $pdo->updateTempFraisForfait($idVisiteur, $mois, $idFraisForfait, $value);
                }
            }
        } elseif ($actionForfait === 'reinitialiser') {
            $pdo->reinitialiserTempFraisForfait($idVisiteur, $mois);
        }

        $elementsForfaitises = $pdo->getCopieFraisForfait($idVisiteur, $mois);
        $elementsHorsForfait = $pdo->getElementsHorsForfait($idVisiteur, $mois);

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

    case 'validerCopieFraisForfait':
        $idVisiteur = $_SESSION['idVisiteur'] ?? null;
        $mois = $_SESSION['moisSelectionne'] ?? null;

        if ($idVisiteur && $mois) {
            $pdo->validerCopieFraisForfait($idVisiteur, $mois);

            // Définir un message de succès dans la session
            $_SESSION['alert'] = 'Fiche de frais validée avec succès.';
        }

        // Réinitialiser la session pour revenir à la sélection
        unset($_SESSION['idVisiteur'], $_SESSION['moisSelectionne'], $_SESSION['nomVisiteur'], $_SESSION['prenomVisiteur']);

        // Rediriger vers la page principale
        header('Location: index.php?uc=validerfrais&action=validerFrais');
        exit();

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;
}