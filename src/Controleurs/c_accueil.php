<?php

/**
 * Gestion de l'accueil
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

namespace Controleurs;

use Outils\Utilitaires;

if (!Utilitaires::estConnecte()) {
    // Redirection ou erreur si l'utilisateur n'est pas connecté
    Utilitaires::ajouterErreur('Accès non autorisé.');
    header('Location: index.php?uc=connexion');
    exit();
}

// Récupérer le type d'utilisateur
$typeUtilisateur = $_SESSION['typeUtilisateur'];

switch ($typeUtilisateur) {
    case 'visiteur':
        // Inclure la vue d'accueil pour les visiteurs
        include PATH_VIEWS . 'v_accueilvisiteur.php';
        break;
    case 'comptable':
        // Inclure la vue d'accueil pour les comptables
        include PATH_VIEWS . 'v_accueilcomptable.php';
        break;
    default:
        Utilitaires::ajouterErreur('Type d\'utilisateur inconnu.');
        header('Location: index.php?uc=connexion');
        exit();
}

