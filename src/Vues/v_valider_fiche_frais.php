<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Validation des fiches de frais</title>
        <link rel="stylesheet" href="./styles/comptable.css">
    </head>
    <body>

        <div class="container">
            <h2>Validation des fiches de frais</h2>

            <!-- Formulaire de sélection du visiteur -->
            <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
                <div class="form-group form-row">
                    <label for="lstVisiteur">Choisir le visiteur :</label>
                    <select name="lstVisiteur" id="lstVisiteur" required>
                        <option value="">Sélectionner un visiteur</option>
                        <?php foreach ($lesVisiteurs as $visiteur): ?>
                            <option value="<?= $visiteur['id'] ?>" <?= isset($idVisiteur) && $idVisiteur == $visiteur['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Affichage du mois sélectionné à droite du visiteur -->
                    <?php if (isset($moisASelectionner) && !empty($moisASelectionner)): ?>
                        <span style="margin-left: 20px; font-weight: bold;">Mois sélectionné : <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?></span>
                    <?php endif; ?>

                    <!-- Bouton Suivant pour afficher les mois -->
                    <button type="submit" class="btn btn-suivant">Suivant</button>
                </div>
            </form>

            <!-- Formulaire de sélection du mois (visible après avoir sélectionné un visiteur) -->
            <?php if (isset($lesMois) && !empty($lesMois)): ?>
                <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
                    <div class="form-group form-row">
                        <label for="lstMois">Choisir un mois :</label>
                        <select name="lstMois" id="lstMois" required>
                            <option value="">Sélectionner un mois</option>
                            <?php foreach ($lesMois as $unMois): ?>
                                <option value="<?= $unMois['mois'] ?>" <?= isset($moisASelectionner) && $moisASelectionner == $unMois['mois'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unMois['numMois'] . '/' . $unMois['numAnnee']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Bouton Valider pour afficher les éléments forfaitisés -->
                        <button type="submit" class="btn btn-valider">Valider</button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Affichage du titre et des éléments forfaitisés -->
            <?php if (isset($infosFicheFrais) && !empty($infosFicheFrais)): ?>
                <div class="elements-forfaitises">
                    <h3>Éléments forfaitisés de <?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?> pour le mois <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?></h3>
                    <div class="form-col">
                        <label for="forfaitEtape">Forfait Étape:</label>
                        <input type="number" id="forfaitEtape" name="forfaitEtape" value="<?= $infosFicheFrais['forfaitEtape'] ?? '' ?>" readonly>

                        <label for="fraisKilometrique">Frais Kilométrique:</label>
                        <input type="number" id="fraisKilometrique" name="fraisKilometrique" value="<?= $infosFicheFrais['fraisKilometrique'] ?? '' ?>" readonly>

                        <label for="nuiteeHotel">Nuitée Hôtel:</label>
                        <input type="number" id="nuiteeHotel" name="nuiteeHotel" value="<?= $infosFicheFrais['nuiteeHotel'] ?? '' ?>" readonly>

                        <label for="repasRestaurant">Repas Restaurant:</label>
                        <input type="number" id="repasRestaurant" name="repasRestaurant" value="<?= $infosFicheFrais['repasRestaurant'] ?? '' ?>" readonly>
                    </div>

                    <!-- Boutons pour Corriger et Rembourser -->
                    <div class="btn-group">
                        <button id="btn-corriger" type="button" class="btn btn-corriger">Corriger</button>
                        <button id="btn-rembourser" type="button" class="btn btn-rembourser">Rembourser</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
