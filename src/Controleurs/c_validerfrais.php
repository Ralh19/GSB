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
<<<<<<< HEAD
        $infosFicheFrais = [];
<<<<<<< HEAD

        // Si un visiteur a été sélectionné
        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            var_dump($idVisiteur); // Déboguer : Afficher l'ID du visiteur pour vérifier la récupération
            // Récupérer les mois associés à ce visiteur
            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
=======
        $moisASelectionner = ''; // Initialiser la variable du mois sélectionné
        $infosVisiteur = []; // Initialiser infosVisiteur comme un tableau vide
        // Si un visiteur a été sélectionné
=======
        $ficheFrais = [];
        $elementsForfaitises = [];
        $elementsHorsForfait = [];
        $moisASelectionner = '';
        $infosVisiteur = [];

>>>>>>> 21eb142 (ajout description éléments hors forfaitisés)
        if (isset($_POST['lstVisiteur'])) {
            $idVisiteur = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
            $_SESSION['idVisiteur'] = $idVisiteur;

            $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

            $infosVisiteur = $pdo->getVisiteurInfo($idVisiteur);

            $_SESSION['nomVisiteur'] = $infosVisiteur['nom'];
            $_SESSION['prenomVisiteur'] = $infosVisiteur['prenom'];
>>>>>>> 9ba10ab (vivienne a disparu passons au élément forfaitisé)
        }

        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
<<<<<<< HEAD
<<<<<<< HEAD

            // Récupérer les éléments forfaitisés pour ce mois et ce visiteur
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
=======
            $moisASelectionner = $mois; 
            $resultatsFicheFrais = $pdo->getLesInfosFicheFrais2($_SESSION['idVisiteur'], $mois);
            $ficheFrais = $resultatsFicheFrais['ficheFrais'];
            $elementsForfaitises = $resultatsFicheFrais['elementsForfaitises'];
=======
            $_SESSION['moisSelectionne'] = $mois;
            $moisASelectionner = $mois;
>>>>>>> 68641ee (buton corriger et reintialiser element forfaitisé marche mais pas valider)

            // Charger les frais pour ce mois
            $elementsForfaitises = $pdo->getCopieFraisForfait($_SESSION['idVisiteur'], $mois);
            $elementsHorsForfait = $pdo->getElementsHorsForfait($_SESSION['idVisiteur'], $mois);
>>>>>>> 21eb142 (ajout description éléments hors forfaitisés)
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
<<<<<<< HEAD
<<<<<<< HEAD
}
=======
}
?>
>>>>>>> 21eb142 (ajout description éléments hors forfaitisés)
=======

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
>>>>>>> 68641ee (buton corriger et reintialiser element forfaitisé marche mais pas valider)
