<?php

/**
 * Vue Accueil
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

?>
<div class="alert alert-warning" role="alert"><strong>Rappel : </strong>Vos frais sont à déclarer au plus tard le dernier jour du mois 
	et vos factures acquittées doivent être arrivées aux services comptables au plus tard le 10 du mois suivant la saisie.
	Les éléments reçus après le 10 seront reportés sur le mois suivant.
</div>
<div id="accueil">
    <h2>
        Gestion des frais<small> - Comptable : 
            <?= $_SESSION['prenom'] . ' ' . $_SESSION['nom'] ?></small>
    </h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a href="index.php?uc=gererFrais&action=saisirFrais"
                           class="btn btn-warning btn-lg" role="button">
                            <span class="glyphicon glyphicon-ok"></span>
                            <br>Valider fiche de frais </a>
                        <a href="index.php?uc=etatFrais&action=selectionnerMois"
                           class="btn btn-warning btn-lg" role="button">
                            <span class="glyphicon glyphicon-eur"></span>
                            <br>Suivre le paiement des fiches de frais</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>