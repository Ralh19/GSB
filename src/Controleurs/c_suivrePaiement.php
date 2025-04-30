<?php

/**
 * Suivi des paiements
 * 
 *  * Ce fichier gère les différentes étapes de suivi et validation des paiements
 * pour les fiches de frais validées. Il permet au comptable de :
 * - Sélectionner un mois parmi ceux ayant des fiches validées
 * - Visualiser un tableau récapitulatif avec pagination
 * - Valider le paiement des fiches de frais des visiteurs
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

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

switch ($action) {
    case 'selectionnerMois':
        $lesMois = $pdo->getLesMoisAvecFichesValides();
        include PATH_VIEWS . 'v_listeMoisPaiement.php';
        break;
    case 'tableauPaiement':
        $lesMois = $pdo->getLesMoisAvecFichesValides();

        // Récupération de la page actuelle ou par défaut à 1
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (!$page || $page < 1) {
            $page = 1;
        }
        // Nombre de visiteurs par page
        $visiteursParPage = 10;

        $moisASelectionner = filter_input(INPUT_POST, 'moisPaiement', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$moisASelectionner) {
            $moisASelectionner = $_SESSION['moisASelectionnerPaiement'] ?? null;
        }

        if (!$moisASelectionner) {
            include PATH_VIEWS . 'v_listeMoisPaiement.php';
            exit();
        }

        $_SESSION['moisASelectionnerPaiement'] = $moisASelectionner;
        $lesVisiteurs = $pdo->getLesVisiteursAvecFichesValides($moisASelectionner);

        if (empty($lesVisiteurs)) {
            $detailsVisiteurs = [];
            $totalPages = 0;
        } else {
            $totalVisiteurs = count($lesVisiteurs);
            $totalPages = ceil($totalVisiteurs / $visiteursParPage);

            $offset = ($page - 1) * $visiteursParPage;
            $visiteursPageCourante = array_slice($lesVisiteurs, $offset, $visiteursParPage);

            $detailsVisiteurs = [];
            foreach ($visiteursPageCourante as $unVisiteur) {
                $idVisiteur = $unVisiteur['idvisiteur'];

                $totalFraisForfait = $pdo->calculerTotalFraisForfait($idVisiteur, $moisASelectionner);
                $totalFraisHorsForfait = $pdo->calculerTotalFraisHorsForfait($idVisiteur, $moisASelectionner);
                $totalGeneral = $totalFraisForfait + $totalFraisHorsForfait;

                $detailsVisiteurs[] = [
                    'idVisiteur' => $idVisiteur,
                    'totalFraisForfait' => $totalFraisForfait,
                    'totalFraisHorsForfait' => $totalFraisHorsForfait,
                    'totalGeneral' => $totalGeneral,
                ];
            }
        }

        include PATH_VIEWS . 'v_listeMoisPaiement.php';
        include PATH_VIEWS . 'v_tableauPaiement.php';
        break;


    case 'validerPaiement':
        $visiteursSelectionnes = filter_input(INPUT_POST, 'visiteurs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $moisASelectionner = $_SESSION['moisASelectionnerPaiement'];
        foreach ($visiteursSelectionnes as $idVisiteur) {
            $pdo->validerPaiementVisiteur($idVisiteur, $moisASelectionner);
        }
        echo '<script>
        if (confirm("Les fiches de frais ont bien été mises en paiement.")) {
            window.location.href = "index.php?uc=suivrePaiement&action=selectionnerMois";
        } 
        </script>';
        exit();
}
