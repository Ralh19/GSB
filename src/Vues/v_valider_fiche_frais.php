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

<<<<<<< HEAD
                    <label for="nuiteeHotel">Nuitée Hôtel:</label>
                    <input type="number" id="nuiteeHotel" name="nuiteeHotel" value="<?= $infosFicheFrais['nuiteeHotel'] ?? '' ?>" readonly>
=======
            <!-- Affichage du titre et des éléments forfaitisés -->
            <?php if (isset($infosFicheFrais) && !empty($infosFicheFrais)): ?>
                <div class="elements-forfaitises">
                    <h3>
                        <!-- Vérifie si les informations du visiteur sont présentes dans la session -->
                        Éléments forfaitisés de 
                        <?= isset($_SESSION['nomVisiteur']) && isset($_SESSION['prenomVisiteur']) ? htmlspecialchars($_SESSION['nomVisiteur'] . ' ' . $_SESSION['prenomVisiteur']) : 'Visiteur inconnu' ?>
                        pour le mois <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?>
                    </h3>
                    <div class="form-col">
                        <label for="forfaitEtape">Forfait Étape:</label>
                        <input type="number" id="forfaitEtape" name="forfaitEtape" value="<?= $infosFicheFrais['forfaitEtape'] ?? '' ?>" readonly>
>>>>>>> 9ba10ab (vivienne a disparu passons au élément forfaitisé)

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