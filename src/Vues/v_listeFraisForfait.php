<div class="row">
    <h2>Renseigner ma fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post" 
              action="index.php?uc=gererFrais&action=validerMajFraisForfait" 
              role="form">
            <fieldset>       
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite'];
                    ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <!-- Section pour le type de véhicule et indemnité kilométrique -->
                <div class="form-group">
                    <label for="typeVehicule">Type de véhicule :</label>
                    <select id="typeVehicule" name="typeVehicule" class="form-control">
                        <option value="4CV Diesel">4CV Diesel</option>
                        <option value="5/6CV Diesel">5/6CV Diesel</option>
                        <option value="4CV Essence">4CV Essence</option>
                        <option value="5/6CV Essence">5/6CV Essence</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="indemniteKilometrique">Indemnité Kilométrique :</label>
                    <input type="text" id="indemniteKilometrique" name="indemniteKilometrique" 
                           value="" class="form-control" readonly>
                </div>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeVehiculeSelect = document.getElementById('typeVehicule');
        const fraisKilometriqueInput = document.getElementById('idFrais');
        const indemniteKilometriqueInput = document.getElementById('indemniteKilometrique');

        const tarifs = {
            '4CV Diesel': 0.52,
            '5/6CV Diesel': 0.58,
            '4CV Essence': 0.62,
            '5/6CV Essence': 0.67
        };

        // Fonction pour calculer l'indemnité kilométrique
        function calculerIndemnite() {
            const typeVehicule = typeVehiculeSelect.value;
            const fraisKilometrique = parseFloat(fraisKilometriqueInput.value) || 0;
            const tarif = tarifs[typeVehicule] || 0;
            const indemnite = fraisKilometrique * tarif;
            indemniteKilometriqueInput.value = indemnite.toFixed(2); // Affiche avec deux décimales
        }

        // Écouter les changements sur le type de véhicule et frais kilométrique
        typeVehiculeSelect.addEventListener('change', calculerIndemnite);
        fraisKilometriqueInput.addEventListener('input', calculerIndemnite);
    });
</script>

