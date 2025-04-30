<?php if ($_SESSION['type_utilisateur'] === "comptable"): ?>
<div class="inline" id="inlineBlock">
    <label for="input-visiteur" id="inlineBlock">Choisir le visiteur : </label>
    <form class="inline" id="inlineBlock" action="index.php?uc=validerFrais&action=selectionnerMois" method="POST" onchange=submit()>
        <select id="visiteursAValider" name="idVisiteurAValider">
            <?php
            foreach ($lesVisiteurs as $unVisiteur) {
                $nom = $unVisiteur['nom'];
                $prenom = $unVisiteur['prenom'];
                $id = $unVisiteur['id'];
                ?>
                
                <option value="<?php echo htmlspecialchars($id); ?>" <?php echo $id === $idVisiteurAValider ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($nom . ' ' . $prenom); ?>
                </option>
                    <?php
                }
                ?>
        </select>
        <input class="btn btn-success" type="submit" value="Suivant">
    </form>
</div>
<?php endif; ?>