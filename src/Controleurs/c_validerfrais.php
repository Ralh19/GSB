<?php

/**
 * Validations des frais
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

use Outils\Utilitaires;

Utilitaires::verifierAccesComptable();

$mois = Utilitaires::getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$idVisiteurAValider = $_SESSION['idVisiteurAValider'] ?? null;

/**
 * Contrôleur : Validation des frais
 * 
 * Liste des actions disponibles :
 * - selectionnerVisiteur : Affiche la liste des visiteurs
 * - selectionnerMois : Permet de choisir un mois à valider
 * - voirEtatFrais : Affiche les frais d’un mois sélectionné
 * - validerMajFraisForfait : Met à jour les frais forfait
 * - validerMajFraisHorsForfait : Met à jour les frais hors forfait
 * - refuserFrais : Marque un frais comme refusé
 * - reporterFrais : Reporte un frais hors forfait au mois suivant
 * - validerFiche : Valide la fiche avec son montant total
 */
switch ($action) {
    case 'selectionnerVisiteur':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        include PATH_VIEWS . 'v_listeVisiteur.php';
        break;

    case 'selectionnerMois':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $idVisiteurAValider = filter_input(INPUT_POST, 'idVisiteurAValider', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $_SESSION['idVisiteurAValider'] = $idVisiteurAValider;
        $lesMois = $pdo->getLesMoisDisponiblesAValider($idVisiteurAValider);
        include PATH_VIEWS . 'v_listeVisiteur.php';
        include PATH_VIEWS . 'v_validerMois.php';
        break;

    case 'voirEtatFrais':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $lesMois = $pdo->getLesMoisDisponiblesAValider($idVisiteurAValider);
        $moisASelectionner = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($moisASelectionner) {
            $_SESSION['moisASelectionner'] = $moisASelectionner;
        } else {
            $moisASelectionner = $_SESSION['moisASelectionner'] ?? null; // Utiliser null si non défini
        }

        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteurAValider, $moisASelectionner);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteurAValider, $moisASelectionner);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteurAValider, $moisASelectionner);

        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = Utilitaires::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        $_SESSION['moisASelectionner'] = $moisASelectionner;


        include PATH_VIEWS . 'v_listeVisiteur.php';
        include PATH_VIEWS . 'v_validerMois.php';
        include PATH_VIEWS . 'v_validationFiche.php';
        break;

    case 'validerMajFraisForfait':
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $moisASelectionner = $_SESSION['moisASelectionner'];
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        $pdo->majFraisForfait($idVisiteurAValider, $moisASelectionner, $lesFrais);
        echo '<script>
        if (confirm("La correction a bien été appliquée.")) {
            window.location.href = "index.php?uc=validerFrais&action=voirEtatFrais";
        } 
        </script>';
        exit();


    case 'validerMajFraisHorsForfait':
        $lesFraisHorsForfait = filter_input(INPUT_POST, 'frais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $moisASelectionner = $_SESSION['moisASelectionner'];
        foreach ($lesFraisHorsForfait as $idFrais => $frais) {
            $date = $frais['date'];
            $libelle = $frais['libelle'];
            $montant = $frais['montant'];
            $pdo->majFraisHorsForfait($idVisiteurAValider, $moisASelectionner, $idFrais, $date, $libelle, $montant);
        }
        echo '<script>
        if (confirm("La correction a bien été appliquée.")) {
            window.location.href = "index.php?uc=validerFrais&action=voirEtatFrais";
        } 
        </script>';
        exit();

    case 'refuserFrais':
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pdo->refuserFraisHorsForfait($idFrais);
        echo '<script>
        if (confirm("La ligne de frais hors forfait a bien été refusée.")) {
            window.location.href = "index.php?uc=validerFrais&action=voirEtatFrais";
        } 
        </script>';
        exit();

    case 'reporterFrais':
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $moisASelectionner = $_SESSION['moisASelectionner'];
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pdo->reporterFraisHorsForfait($idVisiteurAValider, $idFrais, $moisASelectionner);
        echo '<script>
        if (confirm("La ligne de frais hors forfait a bien été reportée au mois prochain.")) {
            window.location.href = "index.php?uc=validerFrais&action=voirEtatFrais";
        } 
        </script>';
        exit();

    case 'validerFiche':
        $idVisiteurAValider = $_SESSION['idVisiteurAValider'];
        $moisASelectionner = $_SESSION['moisASelectionner'];
        $etat = 'VA';
        $montantValide = $pdo->calculerMontantValide($idVisiteurAValider, $moisASelectionner);
        $pdo->validerFicheFrais($idVisiteurAValider, $moisASelectionner, $etat, $montantValide);
        echo '<script>
        if (confirm("La fiche de frais a bien été validée avec un montant total de ' . $montantValide . ' €.")) {
            window.location.href = "index.php";
        } 
        </script>';
        exit();
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteurAValider, $moisASelectionner);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteurAValider, $moisASelectionner);
}
