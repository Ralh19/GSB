<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Choisir le Mois</title>
    </head>
    <body>

        <div class="container">
            <h2>Choisir un Mois</h2>
            <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
                <input type="hidden" name="lstVisiteur" value="<?= htmlspecialchars($idVisiteur) ?>"> <!-- Passer l'ID du visiteur -->
                <div class="form-group">
                    <label for="lstMois">Mois :</label>
                    <select name="lstMois" id="lstMois" required>
                        <?php foreach ($lesMois as $mois): ?>
                            <option value="<?= $mois['mois'] ?>"><?= htmlspecialchars($mois['numMois'] . '/' . $mois['numAnnee']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-valider">Valider</button>
            </form>
        </div>

    </body>
</html>