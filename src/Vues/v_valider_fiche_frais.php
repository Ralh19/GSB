<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Validation des fiches de frais</title>
        <link rel="stylesheet" href="./styles/comptable.css"> <!-- Assurez-vous que le bon CSS est chargé -->
    </head>
    <body>
        <div class="container">
            <div class="choixVisiteurMois">
                <!-- Formulaire pour sélectionner le visiteur et le mois -->
                <form method="post" action="index.php?uc=validerfrais&action=validerfrais">
                    <div class="form-group">
                        <label for="lstVisiteur">Choisir le visiteur :</label>
                        <input list="visiteurs" id="lstVisiteur" name="lstVisiteur" placeholder="Sélectionnez un visiteur" required>
                        <datalist id="visiteurs">
                            <?php foreach ($lesVisiteurs as $visiteur) { ?>
                                <option value="<?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?>">
                                <?php } ?>
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="lstMois">Mois :</label>
                        <input list="moisDisponibles" id="lstMois" name="lstMois" placeholder="Sélectionnez un mois" required>
                        <datalist id="moisDisponibles">
                            <?php foreach ($lesMois as $mois) { ?>
                                <option value="<?= htmlspecialchars($mois['numMois'] . '/' . $mois['numAnnee']) ?>">
                            <?php } ?>
                        </datalist>
                    </div>
                </form>
            </div>
            <h2>Valider la fiche de frais</h2>
            <h3>Eléments forfaitisés</h3>

            <div class="elements-forfaitises">
                <label for="forfaitEtape">Forfait Étape:</label>
                <input type="number" id="forfaitEtape" name="forfaitEtape" value="<?= $infosFicheFrais['forfaitEtape'] ?? '' ?>" readonly>

                <label for="fraisKilometrique">Frais Kilométrique:</label>
                <input type="number" id="fraisKilometrique" name="fraisKilometrique" value="<?= $infosFicheFrais['fraisKilometrique'] ?? '' ?>" readonly>

                <label for="nuiteeHotel">Nuitée Hôtel:</label>
                <input type="number" id="nuiteeHotel" name="nuiteeHotel" value="<?= $infosFicheFrais['nuiteeHotel'] ?? '' ?>" readonly>

                <label for="repasRestaurant">Repas Restaurant:</label>
                <input type="number" id="repasRestaurant" name="repasRestaurant" value="<?= $infosFicheFrais['repasRestaurant'] ?? '' ?>" readonly>
            </div>

            <button type="button" class="btn btn-corriger">Corriger</button>
            <button type="button" class="btn btn-rembourser">Rembourser</button>
        </div>

    </body>
</html>