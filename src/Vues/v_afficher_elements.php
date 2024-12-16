<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Éléments Forfaitisés</title>
</head>

<body>

    <div class="container">
        <h2>Éléments Forfaitisés</h2>
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
    </div>
</body>

</html>