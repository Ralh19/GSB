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
use Outils\Utilitaires;

if (!Utilitaires::estConnecte()) {
// Redirection ou erreur si l'utilisateur n'est pas connecté
    Utilitaires::ajouterErreur('Accès non autorisé.');
    header('Location: index.php?uc=connexion');
    exit();
}

// Vérifiez si l'utilisateur est un visiteur ou un comptable
if (Utilitaires::estConnecteVisiteur()) {
// Inclure la vue pour l'accueil du visiteur
    include PATH_VIEWS . 'v_accueilvisiteur.php';
} elseif (Utilitaires::estConnecteComptable()) {
// Inclure la vue pour l'accueil du comptable
    include PATH_VIEWS . 'v_accueilcomptable.php';
} else {
// Si le type d'utilisateur n'est pas reconnu
    Utilitaires::ajouterErreur('Type d\'utilisateur inconnu.');
    header('Location: index.php?uc=connexion');
    exit();
}


