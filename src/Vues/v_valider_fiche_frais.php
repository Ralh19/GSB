<div class="container">
    <?php if (isset($_SESSION['alert'])): ?>
        <script>
            alert("<?= htmlspecialchars($_SESSION['alert']) ?>");
        </script>
        <?php unset($_SESSION['alert']); // Supprimer l'alerte après affichage ?>
    <?php endif; ?>
    <h2>Validation des fiches de frais</h2>

    <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
        <div class="form-group form-row">
            <label for="lstVisiteur">Choisir le visiteur :</label>
            <input list="visiteurs" name="lstVisiteurNomPrenom" id="lstVisiteur" class="form-control" required placeholder="Saisir le nom du visiteur">
            <datalist id="visiteurs">
                <?php foreach ($lesVisiteurs as $visiteur): ?>
                    <option value="<?= htmlspecialchars($visiteur['nom'] . ' ' . $visiteur['prenom']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
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
            <form method="post" action="index.php?uc=validerfrais&action=corrigerReinitialiserHorsForfait">
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
                        <?php foreach ($elementsHorsForfait as $element): ?>
                            <tr>
                                <td>
                                    <input type="date" 
                                           name="date_<?= htmlspecialchars($element['id'] ?? 'missing_id') ?>" 
                                           value="<?= htmlspecialchars($element['date'] ?? '') ?>" required>
                                </td>
                                <td>
                                    <input type="text" 
                                           name="libelle_<?= htmlspecialchars($element['id'] ?? 'missing_id') ?>" 
                                           value="<?= htmlspecialchars($element['libelle'] ?? '') ?>" required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="montant_<?= htmlspecialchars($element['id'] ?? 'missing_id') ?>" 
                                           value="<?= htmlspecialchars($element['montant'] ?? '') ?>" step="0.01" required>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-valider" name="actionHorsForfait" value="corriger">Corriger</button>
                                    <button type="submit" class="btn btn-reinitialiser" name="actionHorsForfait" value="reinitialiser">Réinitialiser</button>
                                    <button type="submit" class="btn btn-refuser" name="actionHorsForfait" value="refuser_<?= htmlspecialchars($element['id'] ?? '') ?>">Refuser</button>
                                    <span>
                                        <?= isset($element['refuse']) && $element['refuse'] ? '<em>(Refusé)</em>' : '' ?>
                                    </span>
                                    <button type="submit" class="btn btn-reporter" name="actionHorsForfait" value="reporter_<?= isset($element['id']) ? htmlspecialchars($element['id']) : 'missing_id' ?>">Reporter</button>

                                </td>   
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    <?php endif; ?>

    <!-- Justificatifs et boutons -->
    <!-- Justificatifs and final validation -->
    <?php if (isset($moisASelectionner) && !empty($moisASelectionner)): ?>
        <div class="justificatifs-actions">
            <div class="nb-justificatifs">
                <label for="nbJustificatifs">Nombre de justificatifs :</label>
                <input type="number" id="nbJustificatifs" name="nbJustificatifs" 
                       value="<?= htmlspecialchars($ficheFrais['nbJustificatifs'] ?? '0') ?>" >
            </div>
            <div class="btn-group" style="margin-top: 15px;">
                <form method="post" action="index.php?uc=validerfrais&action=validerCopieFraisForfait">
                    <button type="submit" class="btn btn-valider">Valider</button>
                </form>
                <form method="post" action="index.php?uc=validerfrais&action=reinitialiserTout">
                    <button type="submit" class="btn btn-reinitialiser">Réinitialiser</button>
                </form>
            </div>
        </div>

    <?php endif; ?>

</div>
