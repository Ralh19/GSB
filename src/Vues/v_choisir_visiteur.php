<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Choisir le Visiteur</title>
    </head>
    <body>

        <div class="container">
            <h2>Choisir un Visiteur</h2>
            <form method="post" action="index.php?uc=validerfrais&action=choisirMois">
                <div class="form-group">
                    <label for="lstVisiteur">Choisir le visiteur :</label>
                    <select name="lstVisiteur" id="lstVisiteur" required>
                        <?php foreach ($lesVisiteurs as $visiteur): ?>
                            <option value="<?= $visiteur['id'] ?>"><?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-suivant">Suivant</button>
            </form>
        </div>

    </body>
</html>
