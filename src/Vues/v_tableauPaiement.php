<?php if ($_SESSION['type_utilisateur'] === "comptable"): ?>
    <link rel="stylesheet" href="../../public/styles/comptable.css">
    <hr>
    <form action="index.php?uc=suivrePaiement&action=validerPaiement" method="POST">
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                    </th>
                    <th class="idvisiteur">ID du visiteur</th>
                    <th class="totalfraisforfait">Total frais forfait</th>
                    <th class="totalhorsfraisforfait">Total frais hors forfait</th>
                    <th class="total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detailsVisiteurs)): ?>
                    <tr>
                        <td colspan="5">Aucun visiteur trouvé pour cette page.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detailsVisiteurs as $visiteur): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="visiteurs[]" value="<?php echo htmlspecialchars($visiteur['idVisiteur']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($visiteur['idVisiteur']); ?></td>
                            <td><?php echo number_format($visiteur['totalFraisForfait'], 2, ',', ' '); ?> €</td>
                            <td><?php echo number_format($visiteur['totalFraisHorsForfait'], 2, ',', ' '); ?> €</td>
                            <td><?php echo number_format($visiteur['totalGeneral'], 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

        <nav aria-label="Pagination">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?uc=suivrePaiement&action=tableauPaiement&page=<?php echo $page - 1; ?>">Précédent</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?uc=suivrePaiement&action=tableauPaiement&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?uc=suivrePaiement&action=tableauPaiement&page=<?php echo $page + 1; ?>">Suivant</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <button type="submit" class="btn btn-primary">Valider les paiements</button>
    </form>

    <script>
        function toggleSelectAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('input[name="visiteurs[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }
    </script>
<?php endif; ?>