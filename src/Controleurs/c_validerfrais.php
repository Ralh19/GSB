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


        // Si un mois a été sélectionné
        if (isset($_POST['lstMois'])) {
            $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
<<<<<<< HEAD

            // Récupérer les éléments forfaitisés pour ce mois et ce visiteur
            $infosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
=======
            $moisASelectionner = $mois; 
            $resultatsFicheFrais = $pdo->getLesInfosFicheFrais2($_SESSION['idVisiteur'], $mois);
            $ficheFrais = $resultatsFicheFrais['ficheFrais'];
            $elementsForfaitises = $resultatsFicheFrais['elementsForfaitises'];

            $elementsHorsForfait = $pdo->getElementsHorsForfait($_SESSION['idVisiteur'], $mois);
>>>>>>> 21eb142 (ajout description éléments hors forfaitisés)
        }




        include PATH_VIEWS . 'v_valider_fiche_frais.php';
        break;
<<<<<<< HEAD
}
=======
}
?>
>>>>>>> 21eb142 (ajout description éléments hors forfaitisés)
