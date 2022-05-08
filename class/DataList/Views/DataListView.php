<?php
/**
 * DataListView.php
 */
/** @var DataList $dataList */
$dataList = $dataList ?? null;

if (!$dataList) {
    Log::alert("Une erreur est survenue ! La liste n'a pas pu être récupérée !");
    exit();
}

if (count($dataList->getColumnValue()) == 0) {
    echo '<div class="alert alert-warning">Il n\'y a aucun résultat !</div>';
} else {
    ?>

    <table class="<?= $dataList->getTableCss(); ?>" id="<?= $dataList->getId();?>">
        <thead>
        <tr class="<?= $dataList->getTableTrCss(); ?>">
            <?php
            foreach ($dataList->getColumnKey() as $item => $value) {
                echo '<th>'.$value.'</th>';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($dataList->getColumnValue() as $item => $value) {
            echo '<tr>';
            foreach ($dataList->getColumnKey() as $keyItem => $keyValue) {
                echo '<td class="'.$dataList->getTableTdCss().'" onclick="'.$dataList->getOnClickRow($value).'">'.$value[$keyItem].'</td>';
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <?php
}

?>