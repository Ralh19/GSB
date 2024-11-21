<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Validation des fiches de frais</title>
        <link rel="stylesheet" href="./styles/comptable.css">
    </head>
    <body>

        <div id="test" class="container">
            <h2>Validation des fiches de frais</h2>

            <!-- Formulaire de sélection du visiteur -->
            <div class="inputs">  
                <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
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

                        <!-- Affichage du mois sélectionné à droite du visiteur -->
                        <?php if (isset($moisASelectionner) && !empty($moisASelectionner)): ?>
                            <span style="margin-left: 20px; font-weight: bold;">Mois sélectionné : <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?></span>
                        <?php endif; ?>

                        <!-- Bouton Suivant pour afficher les mois -->
                        <button type="submit" class="btn btn-suivant">Suivant</button>
                    </div>
                </form>

                <!-- Formulaire de sélection du mois (visible après avoir sélectionné un visiteur) -->
                <?php if (isset($lesMois) && !empty($lesMois)): ?>
                    <form method="post" action="index.php?uc=validerfrais&action=validerFrais">
                        <div class="form-group form-row">
                            <label for="lstMois">Choisir un mois :</label>
                            <select name="lstMois" id="lstMois" required>
                                <option value="">Sélectionner un mois</option>
                                <?php foreach ($lesMois as $unMois): ?>
                                    <option value="<?= $unMois['mois'] ?>" <?= isset($moisASelectionner) && $moisASelectionner == $unMois['mois'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unMois['numMois'] . '/' . $unMois['numAnnee']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Bouton Valider pour afficher les éléments forfaitisés -->
                            <button type="submit" class="btn btn-valider">Valider</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>



            <?php if (isset($elementsForfaitises) && !empty($elementsForfaitises)): ?>
                <div class="elements-forfaitises">
                    <h3>
                        Éléments forfaitisés de 
                        <?= isset($_SESSION['nomVisiteur']) && isset($_SESSION['prenomVisiteur']) ? htmlspecialchars($_SESSION['nomVisiteur'] . ' ' . $_SESSION['prenomVisiteur']) : 'Visiteur inconnu' ?>
                        pour le mois <?= substr($moisASelectionner, 4, 2) . '/' . substr($moisASelectionner, 0, 4); ?>
                    </h3>
                    <div class="form-col">
                        <?php foreach ($elementsForfaitises as $element): ?>
                            <label for="input_<?= htmlspecialchars($element['idfraisforfait']) ?>"><?= htmlspecialchars($element['libelle']) ?>:</label>
                            <input type="number" 
                                   id="input_<?= htmlspecialchars($element['idfraisforfait']) ?>" 
                                   name="quantite_<?= htmlspecialchars($element['idfraisforfait']) ?>" 
                                   value="<?= $element['quantite'] ?>">
                               <?php endforeach; ?>
                    </div>
                </div>
                <!-- Boutons pour Corriger et Rembourser -->
                <div class="btn-group">
                    <a href="#">                   
                        <button type="button" class="btn btn-corriger">Corriger</button>
                    </a>
                    <a href="#">
                        <button type="button" class="btn btn-reinitialiser">Réinitialiser</button>
                    </a>

                </div>
            <?php endif; ?>
        </div>


        <!-- Section des éléments hors forfait -->
        <?php if (isset($moisASelectionner) && $moisASelectionner !== ''): ?>
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
                        <?php if (!empty($elementsHorsForfait)): ?>
                            <?php foreach ($elementsHorsForfait as $index => $element): ?>
                                <tr>
                                    <td><input type="date" name="date_<?= $index + 1 ?>" value="<?= htmlspecialchars($element['date']) ?>" required></td>
                                    <td><input type="text" name="libelle_<?= $index + 1 ?>" value="<?= htmlspecialchars($element['libelle']) ?>" required></td>
                                    <td><input type="number" name="montant_<?= $index + 1 ?>" value="<?= htmlspecialchars($element['montant']) ?>" step="0.01" required></td>
                                    <td>
                                        <button type="button" class="btn btn-corriger">Corriger</button>
                                        <button type="button" class="btn btn-reinitialiser">Réinitialiser</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Afficher la phrase dans le tableau si aucun élément hors forfait n'est trouvé -->
                            <tr>
                                <td colspan="4" style="text-align: center;">Aucun élément hors forfait trouvé pour ce mois.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </body>
</html>
