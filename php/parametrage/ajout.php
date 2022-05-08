<?php
$id = $_REQUEST['id'] ?? 0;
$table = $_REQUEST['table'] ?? 0;

$row = new stdClass();

switch ($table) {
    case "bien":
        $title = "Nouveau bien";
        $nameId = "bienId";

        $periodes = [];
        $tarifTypes = [];
        $tarifs = [];

        if ($id > 0) {
            $sql = "SELECT * FROM bien WHERE bien.bienId = :id";
            $res = DB::query($sql, [':id' => $id]);
            $row = $res->fetchObject();

            $title = "Modification : ".$row->bienLibelle;

            $sql = "SELECT *
                    FROM periode
                    WHERE periode.periodeActif = 1";
            $res = DB::query($sql);
            while ($periode = $res->fetch(PDO::FETCH_ASSOC)) {
                $periodes[$periode['periodeId']] = $periode;
            }

            $sql = "SELECT tarif_type.*
                    FROM tarif_type
                    WHERE tarif_type.tarifTypeActif = 1
                        AND tarif_type.tarifTypeObjetTable LIKE 'bien'";
            $res = DB::query($sql);
            while ($tarifType = $res->fetch(PDO::FETCH_ASSOC)) {
                $tarifTypes[$tarifType['tarifTypeId']] = $tarifType;
            }

            $sql = "SELECT *
                    FROM bien_tarif
                    WHERE bien_tarif.bienId = :id";
            $res = DB::query($sql, [':id' => $id]);
            while ($tarif = $res->fetch(PDO::FETCH_ASSOC)) {
                $tarifs[$tarif['periodeId']][$tarif['tarifTypeId']] = $tarif;
            }

        } else {
            $row->bienActif = 1;
        }

        break;
    case "periode":
        $title = "Nouvelle période";
        $nameId = "periodeId";

        if ($id > 0) {
            $sql = "SELECT * FROM periode WHERE periode.periodeId = :id";
            $res = DB::query($sql, [':id' => $id]);
            $row = $res->fetchObject();

            $title = "Modification : ".$row->periodeLibelle;
        } else {
            $row->periodeActif = 1;
        }

        break;
    case "site":
        $title = "Nouveau site de réservation";
        $nameId = "siteId";

        if ($id > 0) {
            $sql = "SELECT * FROM site WHERE site.siteId = :id";
            $res = DB::query($sql, [':id' => $id]);
            $row = $res->fetchObject();

            $title = "Modification : ".$row->siteLibelle;
        } else {
            $row->siteActif = 1;
        }

        break;
    default:
		Log::alert("Une erreur est survenue ! Le type de paramétrage n'est pas renseigné");
		exit();
        break;
}
?>

<script>
    function save(sender) {
        if (!validateForm('form-info')) {
            return false;
        }

        let oldHtml = sender.innerHTML;
        sender.innerHTML = '<span><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>';
        sender.disabled = true;

        let url = "ajax.php?do=php/parametrage&action=traitement&todo=save";
        let fd = generateFormData('form-info');

        ajaxCall(url, fd, (response) => {
            if (response > 0) {
                sender.innerHTML = oldHtml;
                sender.disabled = false;

                getPage('<?= $table; ?>');
                <?php if ($table == 'bien' && empty($id)) { ?>
                    addInfo(response);
                    return false;
                <?php } ?>

                closeModal();
            } else {
                alert("Une erreur est survenue !");
                console.log(response);
            }

            sender.innerHTML = oldHtml;
            sender.disabled = false;
            return false;
        });

        return false;
    }
</script>
<div class="card">
	<div class="card-header font-weight-bold">
		<?= $title; ?>
	</div>
	<div class="card-body">
		<form id="form-info" method="post">
            <input type="hidden" name="id" id="id" value="<?= $id; ?>">
            <input type="hidden" name="table" id="table" value="<?= $table; ?>">
            <input type="hidden" name="nameId" id="nameId" value="<?= $nameId; ?>">

			<?php
			switch ($table) {
                case "bien":
                    ?>

                    <div class="form-group">
                        <label for="bienLibelle" class="font-weight-bold">Titre</label>
                        <input type="text" class="form-control" name="DATA[bienLibelle]" id="bienLibelle" value="<?= $row->bienLibelle ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bienLitAdulte" class="font-weight-bold">Nombre de lits pour adultes</label>
                        <input type="number" step="1" class="form-control" name="DATA[bienLitAdulte]" id="bienLitAdulte" value="<?= $row->bienLitAdulte ?? 0; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bienLitEnfant" class="font-weight-bold">Nombre de lits pour enfants</label>
                        <input type="number" step="1" class="form-control" name="DATA[bienLitEnfant]" id="bienLitEnfant" value="<?= $row->bienLitEnfant ?? 0; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bienCommission" class="font-weight-bold">Commission Dracénie BnB</label>
                        <input type="number" step="0.01" class="form-control" name="DATA[bienCommission]" id="bienCommission" value="<?= $row->bienCommission ?? 0; ?>" required>
                    </div>

                    <div class="form-group">
                        <div class="card">
                            <div class="card-header">
                                Tarifs
                            </div>
                            <div class="card-body">
                                <?php
                                if ($id) {
                                    ?>
                                    <table class="table">
                                        <tr>
                                            <th>Période</th>
                                            <?php foreach ($tarifTypes as $tarifType) { ?>
                                                <th><?= $tarifType['tarifTypeLibelle']; ?></th>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        foreach ($periodes as $periodeId => $periode) {
                                            ?>
                                            <tr>
                                                <td><?= $periode['periodeLibelle'];?></td>
                                                <?php foreach ($tarifTypes as $tarifTypeId => $tarifType) { ?>
                                                    <td>
                                                        <input type="number" class="form-control" step="0.01" name="TARIF[<?= $periodeId; ?>][<?= $tarifType['tarifTypeId']; ?>][bienTarifMontant]" value="<?= $tarifs[$periodeId][$tarifTypeId]['bienTarifMontant']?>">
                                                        <input type="hidden" name="TARIF[<?= $periodeId; ?>][<?= $tarifType['tarifTypeId']; ?>][bienTarifId]" value="<?= $tarifs[$periodeId][$tarifTypeId]['bienTarifId']?>">
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-warning">Enregistrez le bien avant de pouvoir ajouter ses tarifs</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                    </div>

                    <?php
                    break;
                case "periode":
                    ?>

                    <div class="form-group">
                        <label for="periodeLibelle" class="font-weight-bold">Titre</label>
                        <input type="text" class="form-control" name="DATA[periodeLibelle]" id="periodeLibelle" value="<?= $row->periodeLibelle ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="periodeCouleur" class="font-weight-bold">Couleur</label>
                        <input type="color" class="form-control" name="DATA[periodeCouleur]" id="periodeCouleur" value="<?= $row->periodeCouleur ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="periodeDateDebut" class="font-weight-bold">Date de début</label>
                        <input type="date" class="form-control" name="DATA[periodeDateDebut]" id="periodeDateDebut" value="<?= $row->periodeDateDebut ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="periodeDateFin" class="font-weight-bold">Date de fin</label>
                        <input type="date" class="form-control" name="DATA[periodeDateFin]" id="periodeDateFin" value="<?= $row->periodeDateFin ?? null; ?>" required>
                    </div>
                    <div>
                        <label for="periodeActif" class="font-weight-bold">Actif</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="DATA[periodeActif]" id="periodeActif_1" value="1" <?= ($row->periodeActif == 1)?'checked':''; ?>>
                            <label class="form-check-label" for="periodeActif_1">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="DATA[periodeActif]" id="periodeActif_0" value="0" <?= ($row->periodeActif == 0)?'checked':''; ?>>
                            <label class="form-check-label" for="periodeActif_0">Non</label>
                        </div>
                    </div>

                    <?php
                    break;
                case "site":
                    ?>

                    <div class="form-group">
                        <label for="siteLibelle" class="font-weight-bold">Titre</label>
                        <input type="text" class="form-control" name="DATA[siteLibelle]" id="siteLibelle" value="<?= $row->siteLibelle ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="siteCouleur" class="font-weight-bold">Couleur</label>
                        <input type="color" class="form-control" name="DATA[siteCouleur]" id="siteCouleur" value="<?= $row->siteCouleur ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="siteCommission" class="font-weight-bold">Commission</label>
                        <input type="number" step="0.01" class="form-control" name="DATA[siteCommission]" id="siteCommission" value="<?= $row->siteCommission ?? null; ?>">
                    </div>
                    <div>
                        <label for="periodeActif" class="font-weight-bold">Actif</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="DATA[siteActif]" id="siteActif_1" value="1" <?= ($row->siteActif == 1)?'checked':''; ?>>
                            <label class="form-check-label" for="siteActif_1">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="DATA[siteActif]" id="siteActif_0" value="0" <?= ($row->siteActif == 0)?'checked':''; ?>>
                            <label class="form-check-label" for="siteActif_0">Non</label>
                        </div>
                    </div>

                    <?php
                    break;
			}
			?>

            <div class="float-right">
                <button class="btn btn-primary" onclick="closeModal()">
                    <span>
                        <i class="fa fa-times"></i>&nbsp;Fermer
                    </span>
                </button>
                <button type="button" class="btn btn-success" onclick="save(this)">
                    <span>
                        <i class="fa fa-save"></i>&nbsp;Enregistrer
                    </span>
                </button>
            </div>
		</form>
	</div>
</div>
