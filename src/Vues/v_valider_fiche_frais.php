<div class="container">
    <?php if (isset($_SESSION['alert'])): ?>
        <script>
            alert("<?= htmlspecialchars($_SESSION['alert']) ?>");
        </script>
        <?php unset($_SESSION['alert']); // Supprimer l'alerte après affichage ?>
    <?php endif; ?>
    <h2>Validation des fiches de frais</h2>

    <!-- Formulaire de sélection du visiteur -->
    <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
        <div class="form-group form-row">
            <label for="lstVisiteur">Choisir le visiteur :</label>
            <select name="lstVisiteur" id="lstVisiteur" required>
                <option value="">Sélectionner un visiteur</option>
                <?php foreach ($lesVisiteurs as $visiteur): ?>
                    <option value="<?= htmlspecialchars($visiteur['id']) ?>" 
                            <?= isset($idVisiteur) && $idVisiteur == $visiteur['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($moisASelectionner) && !empty($moisASelectionner)): ?>
                <span style="margin-left: 20px; font-weight: bold;">
                    Mois sélectionné : <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?>
                </span>
            <?php endif; ?>
            <button type="submit" class="btn btn-suivant">Suivant</button>
        </div>
    </form>

    <!-- Formulaire de sélection du mois -->
    <?php if (isset($lesMois) && !empty($lesMois)): ?>
        <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
            <div class="form-group form-row">
                <label for="lstMois">Choisir un mois :</label>
                <select name="lstMois" id="lstMois" required>
                    <option value="">Sélectionner un mois</option>
                    <?php foreach ($lesMois as $unMois): ?>
                        <option value="<?= htmlspecialchars($unMois['mois']) ?>" 
                                <?= isset($moisASelectionner) && $moisASelectionner == $unMois['mois'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unMois['numMois'] . '/' . $unMois['numAnnee']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-valider">Valider</button>
            </div>
        </form>
    <?php endif; ?>

    <!-- Section des éléments forfaitisés -->
    <?php if (isset($elementsForfaitises) && !empty($elementsForfaitises)): ?>
        <div class="elements-forfaitises">
            <h3>
                Éléments forfaitisés de 
                <?= isset($_SESSION['nomVisiteur'], $_SESSION['prenomVisiteur']) ? htmlspecialchars($_SESSION['nomVisiteur'] . ' ' . $_SESSION['prenomVisiteur']) : 'Visiteur inconnu' ?>
                pour le mois <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?>
            </h3>
            <form method="post" action="index.php?uc=validerfrais&action=corrigerReinitialiserForfait">
                <div class="form-col">
                    <?php foreach ($elementsForfaitises as $element): ?>
                        <label for="input_<?= htmlspecialchars($element['idfraisforfait']) ?>">
                            <?= htmlspecialchars($element['libelle']) ?> :
                        </label>
                        <input type="number" 
                               id="input_<?= htmlspecialchars($element['idfraisforfait']) ?>" 
                               name="quantite_<?= htmlspecialchars($element['idfraisforfait']) ?>" 
                               value="<?= htmlspecialchars($element['quantite']) ?>">
                           <?php endforeach; ?>
                </div>
                <!-- Boutons Corriger et Réinitialiser -->
                <div class="btn-group">
                    <button type="submit" class="btn btn-valider" name="actionForfait" value="corriger">Corriger</button>
                    <button type="submit" class="btn btn-reinitialiser" name="actionForfait" value="reinitialiser">Réinitialiser</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Section des éléments hors forfait -->
    <?php if (isset($elementsHorsForfait) && !empty($elementsHorsForfait)): ?>
        <div class="elements-hors-forfait">
            <h3>Descriptif des éléments hors forfait</h3>
            <table class="table-hors-forfait">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elementsHorsForfait as $index => $element): ?>
                        <tr>
                            <td><input type="date" name="date_<?= $index ?>" value="<?= htmlspecialchars($element['date']) ?>" readonly></td>
                            <td><input type="text" name="libelle_<?= $index ?>" value="<?= htmlspecialchars($element['libelle']) ?>" readonly></td>
                            <td><input type="number" name="montant_<?= $index ?>" value="<?= htmlspecialchars($element['montant']) ?>" step="0.01" readonly></td>
                            <td>
                                <button type="button" class="btn btn-valider">Corriger</button>
                                <button type="button" class="btn btn-reinitialiser">Réinitialiser</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Section des justificatifs -->
    <?php if (isset($moisASelectionner) && !empty($moisASelectionner)): ?>
        <div class="justificatifs-actions">
            <div class="nb-justificatifs">
                <label for="nbJustificatifs">Nombre de justificatifs :</label>
                <input type="number" id="nbJustificatifs" name="nbJustificatifs" 
                       value="<?= htmlspecialchars($ficheFrais['nbJustificatifs'] ?? '0') ?>" readonly>
            </div>
            <div class="btn-group" style="margin-top: 15px;">
                <form method="post" action="index.php?uc=validerfrais&action=validerCopieFraisForfait">
                    <button type="submit" class="btn btn-valider">Valider</button>
                </form>
                <form method="post" action="index.php?uc=validerfrais&action=reinitialiserForfait">
                    <button type="submit" class="btn btn-reinitialiser">Réinitialiser</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
