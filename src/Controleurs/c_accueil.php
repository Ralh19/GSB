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
namespace Outils; // Assurez-vous que le namespace est correct

use Outils\Utilitaires; //

if (!Utilitaires::estConnecteVisiteur()) {
    // Redirection ou erreur si l'utilisateur n'est pas connecté
    Utilitaires::ajouterErreur('Accès non autorisé.');
    header('Location: index.php?uc=connexion');
    include PATH_VIEWS . 'v_erreurs.php';
    exit();
}

// Récupérer les informations supplémentaires si nécessaire
$idVisiteur = $_SESSION['idVisiteur']; // Exemple d'ID du visiteur
// Ici, tu peux ajouter la logique pour récupérer des données spécifiques au visiteur

// Inclure la vue de l'accueil visiteur
include PATH_VIEWS . 'v_accueilvisiteur.php'; // Page d'accueil pour les visiteurs

