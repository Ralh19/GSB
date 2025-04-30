<?php

/**
 * Vue Liste des frais au forfait
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 * @link      https://getbootstrap.com/docs/3.3/ Documentation Bootstrap v3
 */

?>
<div class="row">    
    <h2>Renseigner ma fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Éléments forfaitisés</h3>
    <div class="col-md-6">
        <form method="post" 
              action="index.php?uc=gererFrais&action=validerMajFraisForfait" 
              role="form">
            <fieldset>       
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $quantite = $unFrais['quantite'];

                    // Si c’est un frais véhicule, on le gère à part
                    if (in_array($idFrais, ['KM', 'V4', 'V5', 'E4', 'E5'])) {
                        $typesVehicules = [
                            'V4' => 'Véhicule 4CV Diesel',
                            'V5' => 'Véhicule 5/6CV Diesel',
                            'E4' => 'Véhicule 4CV Essence',
                            'E5' => 'Véhicule 5/6CV Essence'
                        ];
                        ?>
                        <div class="form-group">
                            <label for="vehiculeType">Véhicule</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <select name="vehicule[id]" id="vehiculeType" class="form-control">
                                        <option value="">-- Sélectionner --</option>
                                        <?php foreach ($typesVehicules as $code => $label) { ?>
                                            <option value="<?php echo $code ?>" <?php if ($code === $idFrais) echo 'selected'; ?>>
                                                <?php echo $label ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <input type="text" name="vehicule[quantite]" class="form-control" 
                                           value="<?php echo $quantite ?>" placeholder="Km">
                                </div>
                            </div>
                        </div>
                        <?php
                        // On a géré ce champ, on saute à la prochaine itération
                        continue;
                    }

                    // Affichage classique pour les autres frais
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    ?>
                    <div class="form-group">
                        <label for="frais_<?php echo $idFrais ?>"><?php echo $libelle ?></label>
                        <input type="text" id="frais_<?php echo $idFrais ?>" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                <?php } ?>

                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>
