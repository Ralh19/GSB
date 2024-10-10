<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace Outils; // Assurez-vous que le namespace est correct

use Outils\Utilitaires; //

if (!Utilitaires::estConnecteComptable()) {
    // Redirection ou erreur si l'utilisateur n'est pas connecté
    Utilitaires::ajouterErreur('Accès non autorisé.');
    header('Location: index.php?uc=connexion');
    exit();
}

// Récupérer les informations supplémentaires si nécessaire
$idComptable = $_SESSION['idComptable']; // Exemple d'ID du comptable
// Ici, tu peux ajouter la logique pour récupérer des données spécifiques au comptable

// Inclure la vue de l'accueil comptable
include PATH_VIEWS . 'v_accueilcomptable.php'; // Page d'accueil pour les comptables