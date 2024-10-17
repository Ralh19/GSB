<?php

use Outils\Utilitaires;

// Vérifiez si l'utilisateur est connecté
if (!Utilitaires::estConnecte() || !Utilitaires::estConnecteComptable()) {
    // Redirigez vers une page d'erreur ou d'authentification
    header('Location: index.php?uc=connexion');
    exit;
}

// Récupérez la liste des visiteurs
$lesVisiteurs = $pdo->getLesVisiteurs(); // Vous devrez avoir une méthode pour récupérer les visiteurs

// Récupérez la liste des mois disponibles
$lesMois = $pdo->getLesMoisDisponiblesPourComptable(); // Adapté pour le comptable

// Affichez la vue
include PATH_VIEWS . 'v_valider_fiche_frais.php';