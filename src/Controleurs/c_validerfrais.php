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

        if (isset($_POST['lstVisiteurNomPrenom'])) {
            $nomPrenom = filter_input(INPUT_POST, 'lstVisiteurNomPrenom', FILTER_SANITIZE_STRING);
            $idVisiteur = $pdo->getIdVisiteurParNomPrenom($nomPrenom); // Ajoutez cette méthode dans PdoGsb

            if ($idVisiteur) {
                $_SESSION['idVisiteur'] = $idVisiteur;

                // Charger les mois disponibles pour ce visiteur
                $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

                // Charger les infos du visiteur pour affichage
                $infosVisiteur = $pdo->getVisiteurInfo($idVisiteur);
                $_SESSION['nomVisiteur'] = $infosVisiteur['nom'];
                $_SESSION['prenomVisiteur'] = $infosVisiteur['prenom'];
            } else {
                Utilitaires::ajouterErreur("Visiteur non trouvé : $nomPrenom");
                include PATH_VIEWS . 'v_erreurs.php';
            }
        }

        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
            $_SESSION['moisSelectionne'] = $mois;
            $moisASelectionner = $mois;

            // Copier les frais forfaitisés dans la table temporaire
            $pdo->copierFraisForfaitDansTemp($_SESSION['idVisiteur'], $mois);
            $pdo->copierHorsForfaitDansTemp($_SESSION['idVisiteur'], $mois);

            // Charger les frais depuis la table temporaire
            $elementsForfaitises = $pdo->getCopieFraisForfait($_SESSION['idVisiteur'], $mois);
            $elementsHorsForfait = $pdo->getElementsHorsForfait($_SESSION['idVisiteur'], $mois);
        }

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

    case 'validerCopieFraisForfait':

        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        // Validation : remplacement des données originales par la copie
        $pdo->validerCopieFraisForfait($idVisiteur, $mois);

        // Nettoyage : suppression des données temporaires
        $pdo->clearTempFraisForfait($idVisiteur, $mois);

        // Recharger les données pour afficher la page correctement
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $moisASelectionner = '';
        $elementsForfaitises = [];
        $elementsHorsForfait = [];

        $_SESSION['alert'] = 'Fiche de frais validée avec succès.';
        header('Location: index.php?uc=validerfrais&action=validerFrais');
        exit;

    case 'validerHorsForfait':
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        $pdo->validerTempHorsForfait($idVisiteur, $mois);

        $_SESSION['alert'] = 'Les éléments hors forfait ont été validés avec succès.';
        header('Location: index.php?uc=validerfrais&action=validerFrais');
        exit;

    case 'corrigerReinitialiserForfait':
        $actionForfait = filter_input(INPUT_POST, 'actionForfait', FILTER_SANITIZE_STRING);
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];
        $moisASelectionner = $mois;

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
}