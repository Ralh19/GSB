<?php

/**
 * Gestion de la connexion
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

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (!$uc) {
    $uc = 'demandeconnexion';
}

switch ($action) {
    case 'demandeConnexion':
        include PATH_VIEWS . 'v_connexion.php';
        break;
    case 'valideConnexion':
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $utilisateur = $pdo->getInfosUtilisateur($login, $mdp);

        if ($utilisateur) {
            $id = $utilisateur['id'];
            $nom = $utilisateur['nom'];
            $prenom = $utilisateur['prenom'];
            $type = $utilisateur['type'];
            Utilitaires::connecter($id, $nom, $prenom, $type);
            if (Utilitaires::estConnecteVisiteur()) {
                header('Location: index.php?uc=accueil');
            } elseif (Utilitaires::estConnecteComptable()) {
                header('Location: index.php?uc=accueilComptable');
            } else {
                // Si personne n'est connecté, rediriger vers la page de connexion
                header('Location: index.php?uc=connexion');
            }
            exit();
        } else {
            Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS . 'v_connexion.php';
        }
        break;
    default:
        include PATH_VIEWS . 'v_connexion.php';
        break;
}
