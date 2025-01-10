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
            $nomPrenom = filter_input(INPUT_POST, 'lstVisiteurNomPrenom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

    case 'validerToutesCopiesFrais':
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        // 1. Valider les éléments forfaitisés
        $pdo->validerCopieFraisForfait($idVisiteur, $mois);

        // 2. Valider les éléments hors forfaitisés
        $pdo->validerTempHorsForfait($idVisiteur, $mois);

        // 3. Gérer les éléments hors forfait reportés (si nécessaire)
        $elementsReportes = $pdo->getHorsForfaitReportes($idVisiteur, $mois);
        foreach ($elementsReportes as $element) {
            $moisSuivant = $pdo->calculerMoisSuivant($mois);
            $pdo->ajouterHorsForfait($idVisiteur, $moisSuivant, $element['libelle'], $element['montant'], $element['date']);
            $pdo->supprimerTempHorsForfait($idVisiteur, $mois, $element['id']);
        }

        // 4. Nettoyer les tables temporaires
        $pdo->clearTempFraisForfait($idVisiteur, $mois);
        $pdo->clearTempHorsForfait($idVisiteur, $mois);

        // Message de confirmation
        $_SESSION['alert'] = 'Toutes les modifications ont été validées avec succès.';

        // Redirection pour rafraîchir la vue
        header('Location: index.php?uc=validerfrais&action=validerFrais');
        exit();

    case 'validerHorsForfait':
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        // Call the method to replace original data with the temporary table
        $pdo->validerTempHorsForfait($idVisiteur, $mois);

        $_SESSION['alert'] = 'Les éléments hors forfait ont été validés avec succès.';

        // Clear session and redirect to refresh the view
        unset($_SESSION['idVisiteur'], $_SESSION['moisSelectionne']);
        header('Location: index.php?uc=validerfrais&action=validerFrais');
        exit;

    case 'corrigerReinitialiserForfait':
        $actionForfait = filter_input(INPUT_POST, 'actionForfait', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

    case 'corrigerReinitialiserHorsForfait':
        $actionHorsForfait = filter_input(INPUT_POST, 'actionHorsForfait', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        if ($actionHorsForfait === 'corriger') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'libelle_') === 0) {
                    $idFrais = str_replace('libelle_', '', $key);
                    $pdo->updateTempHorsForfaitLibelle($idVisiteur, $mois, $idFrais, $value);
                }
                if (strpos($key, 'montant_') === 0) {
                    $idFrais = str_replace('montant_', '', $key);
                    $pdo->updateTempHorsForfaitMontant($idVisiteur, $mois, $idFrais, $value);
                }
                if (strpos($key, 'date_') === 0) {
                    $idFrais = str_replace('date_', '', $key);
                    $pdo->updateTempHorsForfaitDate($idVisiteur, $mois, $idFrais, $value);
                }
            }
        } elseif ($actionHorsForfait === 'reinitialiser') {
            $pdo->reinitialiserTempHorsForfait($idVisiteur, $mois);
        } elseif (strpos($actionHorsForfait, 'refuser_') === 0) {
            $idFrais = str_replace('refuser_', '', $actionHorsForfait);
            $pdo->marquerHorsForfaitCommeRefuse($idVisiteur, $mois, $idFrais);
        } elseif (strpos($actionHorsForfait, 'reporter_') === 0) {
            $idFrais = str_replace('reporter_', '', $actionHorsForfait);
            if ($idFrais) {
                // Calculer le mois suivant
                $moisSuivant = $pdo->calculerMoisSuivant($mois);

                // Reporter l'élément dans le mois suivant
                $pdo->reporterHorsForfait($idVisiteur, $mois, $idFrais, $moisSuivant);

                // Ajouter une alerte
                $_SESSION['alert'] = 'L\'élément a bien été reporté au mois suivant.';
            }
        }



        // Recharger les données pour affichage
        $moisASelectionner = $mois;
        $elementsForfaitises = $pdo->getCopieFraisForfait($idVisiteur, $mois);
        $elementsHorsForfait = $pdo->getTempHorsForfait($idVisiteur, $mois);

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;

    case 'reinitialiserTout':
        $idVisiteur = $_SESSION['idVisiteur'];
        $mois = $_SESSION['moisSelectionne'];

        if ($idVisiteur && $mois) {
            // Reinitialize both forfaitized and non-forfaitized elements
            $pdo->reinitialiserTempFraisForfait($idVisiteur, $mois);
            $pdo->reinitialiserTempHorsForfait($idVisiteur, $mois);

            // Set a success message
            $_SESSION['alert'] = 'Toutes les modifications ont été réinitialisées.';
        }

        $moisASelectionner = $mois;

        // Reload data for display
        $elementsForfaitises = $pdo->getCopieFraisForfait($idVisiteur, $mois);
        $elementsHorsForfait = $pdo->getTempHorsForfait($idVisiteur, $mois);

        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;
}