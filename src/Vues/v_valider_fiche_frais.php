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
                        <select name="lstVisiteur" id="lstVisiteur" required>
                            <?php foreach ($lesVisiteurs as $visiteur) { ?>
                                <option value="<?= $visiteur['id'] ?>"><?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lstMois">Mois :</label>
                        <select name="lstMois" id="lstMois" required>
                            <?php foreach ($lesMois as $mois) { ?>
                                <option value="<?= $mois['mois'] ?>"><?= htmlspecialchars($mois['numMois'] . '/' . $mois['numAnnee']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </form>
            </div>
            <h2>Valider la fiche de frais</h2>
            <h3>Eléments forfaitisés</h3>

        </div>
    </body>
</html>