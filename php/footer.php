<?php
$tabLien = [];

$sql = "SELECT *
        FROM lien
        WHERE lien.lienType IS NULL
            AND lien.lienActif = 1
        ORDER BY lien.lienOrdre ASC";
$res = DB::query($sql);
while ($row = $res->fetch()) {
    $row['lienWidth'] = round(100/$res->rowCount());
    $tabLien[$row['lienId']] = $row;
}
?>

<footer class="font-weight-bold h6 pl-3 d-flex">
    <?php
    foreach ($tabLien as $lienId => $lien) {
        ?>
        <div class="mr-4 text-center" style="font-size: 22px;width: <?= $lien['lienWidth']; ?>%" onclick="redirect('index.php?do=<?= $lien['lienDo']; ?>&action=<?= $lien['lienAction']; ?>')">
            <i class="<?= $lien['lienIcone']; ?>"></i>
        </div>
        <?php
    }
    ?>
</footer>