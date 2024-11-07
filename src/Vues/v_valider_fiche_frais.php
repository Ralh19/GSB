<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Validation des fiches de frais</title>
    </head>
    <body>

        <div class="container">
            <h2>Valider la fiche de frais</h2>

            <!-- Formulaire pour sélectionner le visiteur et le mois -->
            <form method="post" action="index.php?uc=validerfrais&action=validerfrais">
                <div class="form-group">
                    <label for="lstVisiteur">Choisir le visiteur :</label>
                    <select name="lstVisiteur" id="lstVisiteur">
                        <?php foreach ($lesVisiteurs as $visiteur) { ?>
                            <option value="<?= $visiteur['id'] ?>"><?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lstMois">Mois :</label>
                    <select name="lstMois" id="lstMois">
                        <?php foreach ($lesMois as $mois) { ?>
                            <option value="<?= $mois['mois'] ?>"><?= htmlspecialchars($mois['numMois'] . '/' . $mois['numAnnee']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-valider">Afficher</button>
            </form>

            <!-- Section des éléments forfaitisés -->
            <?php if (isset($lesFraisForfait)) { ?>
                <h3>Éléments forfaitisés</h3>
                <table>
                    <tr>
                        <th>Libellé</th>
                        <th>Quantité</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($lesFraisForfait as $frais) { ?>
                        <tr>
                            <td><?= htmlspecialchars($frais['libelle']) ?></td>
                            <td><?= htmlspecialchars($frais['quantite']) ?></td>
                            <td>
                                <button class="btn btn-corriger">Corriger</button>
                                <button class="btn btn-refuser">Refuser</button>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

            <!-- Section des éléments hors forfait -->
            <?php if (isset($lesFraisHorsForfait)) { ?>
                <h3>Descriptif des éléments hors forfait</h3>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($lesFraisHorsForfait as $frais) { ?>
                        <tr>
                            <td><?= htmlspecialchars($frais['date']) ?></td>
                            <td><?= htmlspecialchars($frais['libelle']) ?></td>
                            <td><?= htmlspecialchars($frais['montant']) ?> €</td>
                            <td>
                                <button class="btn btn-corriger">Corriger</button>
                                <button class="btn btn-refuser">Reporter</button>
                                <button class="btn btn-refuser">Refuser</button>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

            <!-- Validation finale de la fiche -->
            <?php if (isset($infosFicheFrais)) { ?>
                <form method="post" action="index.php?uc=validerfrais&action=validerFiche">
                    <div class="form-group">
                        <label for="nbJustificatifs">Nombre de justificatifs :</label>
                        <input type="text" id="nbJustificatifs" name="nbJustificatifs" value="<?= htmlspecialchars($infosFicheFrais['nbJustificatifs']) ?>">
                    </div>
                    <button type="submit" class="btn btn-valider">Valider</button>
                    <button type="button" class="btn btn-refuser">Refuser</button>
                </form>
            <?php } ?>
        </div>

    </body>
</html>