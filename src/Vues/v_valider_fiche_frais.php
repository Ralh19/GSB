<h2>Valider fiche de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur :</h3>
    </div>
    <div class="col-md-4">
        <form action="index.php?uc=validerFrais&action=valider" method="post" role="form">
            <div class="form-group">
                <label for="lstVisiteur">Visiteur : </label>
                <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                    <?php foreach ($lesVisiteurs as $unVisiteur) : ?>
                        <option value="<?php echo $unVisiteur['idVisiteur']; ?>">
                            <?php echo $unVisiteur['nom'] . ' ' . $unVisiteur['prenom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="lstMois">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php foreach ($lesMois as $unMois) : ?>
                        <option value="<?php echo $unMois['mois']; ?>">
                            <?php echo substr($unMois['mois'], 4, 2) . '/' . substr($unMois['mois'], 0, 4); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input id="ok" type="submit" value="Valider" class="btn btn-success" role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" role="button">
        </form>
    </div>
</div>