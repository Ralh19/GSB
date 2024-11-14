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

            <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
                <!-- Sélection du Visiteur -->
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

                    <!-- Sélection du Mois -->
                    <label for="lstMois">Mois :</label>
                    <select name="lstMois" id="lstMois" required>
                        <option value="">Sélectionner un mois</option>
                        <?php foreach ($lesMois as $mois): ?>
                            <option value="<?= $mois['mois'] ?>" <?= isset($moisSelectionne) && $moisSelectionne == $mois['mois'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mois['numMois'] . '/' . $mois['numAnnee']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Bouton pour Valider -->
                <div class="form-row">
                    <button type="submit" class="btn btn-valider">Valider</button>
                </div>
            </form>

            <!-- Affichage des éléments forfaitisés -->
            <div class="elements-forfaitises">
                <h3>Éléments forfaitisés</h3>
                <div class="form-row">
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
                    <button type="button" class="btn btn-corriger">Corriger</button>
                    <button type="button" class="btn btn-rembourser">Rembourser</button>
                </div>
            </div>
        </div>
    </body>
</html>