<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle'];
                ?>
                <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $quantite = $unFraisForfait['quantite'];
                ?>
                <td class="qteForfait"><?php echo $quantite ?> </td>
                <?php
            }
            ?>
        </tr>
    </table>
    <!-- Affichage spécifique de l'indemnité kilométrique -->
<?php if (isset($indemniteKilometrique) && isset($typeVehicule)): ?>
        <table class="table table-bordered table-responsive">
            <tr>
                <th>Type de véhicule</th>
                <th>Indemnité Kilométrique</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($typeVehicule); ?></td>
                <td><?php echo number_format($indemniteKilometrique, 2); ?> €</td>
            </tr>
        </table>
<?php endif; ?>
</div>