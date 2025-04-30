<?php

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

        $visiteur = false;
        $comptable = false;

        // Visiteur
        $hashVisiteur = $pdo->getMdpVisiteur($login);
        if ($hashVisiteur && password_verify($mdp, $hashVisiteur)) {
            $visiteur = $pdo->getInfosVisiteur($login);
        }

        // Comptable
        $hashComptable = $pdo->getMdpComptable($login);
        if ($hashComptable && password_verify($mdp, $hashComptable)) {
            $comptable = $pdo->getInfosComptable($login);
        }

        // Aucun utilisateur valide
        if (!$visiteur && !$comptable) {
            Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS . 'v_connexion.php';
            break;
        }

        // Envoi du code A2F si Visiteur
        if ($visiteur) {
            Utilitaires::connecter($visiteur['id'], $visiteur['nom'], $visiteur['prenom']);
            $_SESSION['type_utilisateur'] = 'visiteur';
            $_SESSION['idVisiteur'] = $visiteur['id'];
            $code = rand(1000, 9999);
            $pdo->setCodeA2f($visiteur['id'], $code);
            file_put_contents(__DIR__ . '/../../tests/codea2f.txt', "Code pour $login : $code\n", FILE_APPEND);

            //mail($visiteur['email'], '[GSB-AppliFrais] Code de vérification', "Voici votre code : $code");

            include PATH_VIEWS . 'v_code2facteurs.php';
            break;
        }

        // Envoi du code A2F si Comptable
        if ($comptable) {
            Utilitaires::connecter($comptable['id'], $comptable['nom'], $comptable['prenom']);
            $_SESSION['type_utilisateur'] = 'comptable';
            $_SESSION['idComptable'] = $comptable['id'];
            $code = rand(1000, 9999);
            $pdo->setCodeA2fComptable($comptable['id'], $code);
            file_put_contents(__DIR__ . '/../../tests/codea2f.txt', "Code pour $login : $code\n", FILE_APPEND);

            //mail($comptable['email'], '[GSB-AppliFrais] Code de vérification', "Voici votre code : $code");

            include PATH_VIEWS . 'v_code2facteurs.php';
            break;
        }

        break;

        case 'valideA2fConnexion':
            $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT);
            $type = $_SESSION['type_utilisateur'] ?? '';
        
            if ($type === 'visiteur') {
                $codeAttendu = $pdo->getCodeVisiteur($_SESSION['idVisiteur']);
            } elseif ($type === 'comptable') {
                $codeAttendu = $pdo->getCodeComptable($_SESSION['idComptable']);
            } else {
                $codeAttendu = null;
            }
        
            if (!$codeAttendu || $code != $codeAttendu) {
                Utilitaires::ajouterErreur('Code de vérification incorrect');
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_code2facteurs.php';
            } else {
                Utilitaires::connecterA2f($code);
                header('Location: index.php');
            }
            break;
        

    default:
        include PATH_VIEWS . 'v_connexion.php';
        break;
}
