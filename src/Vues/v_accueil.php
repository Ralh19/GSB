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
<?php if (isset($_SESSION['type_utilisateur']) && $_SESSION['type_utilisateur'] === "visiteur"): ?>
    <div class="alert alert-warning" role="alert">
        <strong>Rappel : </strong>Vos frais sont à déclarer au plus tard le dernier jour du mois 
        et vos factures acquittées doivent être arrivées aux services comptables au plus tard le 10 du mois suivant la saisie.
        Les éléments reçus après le 10 seront reportés sur le mois suivant.
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['type_utilisateur']) && $_SESSION['type_utilisateur'] === "comptable"): ?>
    <link rel="stylesheet" href="./styles/comptable.css">
<?php endif; ?>


<div id="accueil" class="<?= isset($_SESSION['type_utilisateur']) ? $_SESSION['type_utilisateur'] : '' ?>">
    <h2>
        Gestion des frais
        <small> - 
            <?=
            ($_SESSION['type_utilisateur'] === "visiteur") ? "Visiteur" . ' : ' . $_SESSION['prenom'] . ' ' . $_SESSION['nom'] : "Comptable" . ' : ' . $_SESSION['prenom'] . ' ' . $_SESSION['nom']
            ?>
        </small>
    </h2>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <?php if (isset($_SESSION['type_utilisateur']) && $_SESSION['type_utilisateur'] === "visiteur"): ?>
                            <a href="index.php?uc=gererFrais&action=saisirFrais"
                               class="btn btn-success btn-lg" role="button">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <br>Renseigner la fiche de frais</a>

                            <a href="index.php?uc=etatFrais&action=selectionnerMois"
                               class="btn btn-primary btn-lg" role="button">
                                <span class="glyphicon glyphicon-list-alt"></span>
                                <br>Afficher mes fiches de frais</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['type_utilisateur']) && $_SESSION['type_utilisateur'] === "comptable"): ?>
                            <a href="index.php?uc=validerFrais&action=selectionnerVisiteur"
                               class="btn btn-primary btn-lg" role="button">
                                <span class="glyphicon glyphicon-list-alt"></span>
                                <br>Valider une fiche de frais</a>
                            <a href="index.php?uc=suivrePaiement&action=selectionnerMois"
                               class="btn btn-primary btn-lg" role="button">
                                <span class="glyphicon glyphicon-euro"></span>
                                <br>Suivre le paiement des fiches de frais</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>