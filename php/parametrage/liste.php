<?php
$table = $_REQUEST['table'] ?? null;

if (empty($table)) {
	Log::alert("Une erreur est survenue ! La liste n'a pas pu être récupérée !");
	exit();
}

// Dernier paramétrage
$_SESSION[getSessionName()]['settingsTable'] = $table;

switch ($table) {
    case "bien":
        $title = "Liste des biens";

        $sql = "SELECT *
                FROM bien";
        $dataList = new DataList($sql);
        $dataList->setOrderBy(['bienLibelle' => 'ASC']);
        $dataList->setColumn("bienLibelle","Titre");
        $dataList->setColumn("bienActif","Actif",DataList::TYPE_BOOL);
        $dataList->setOnClickParams('bienId');

        break;
    case "periode":
        $title = "Liste des périodes";

        $sql = "SELECT *, 
                    CONCAT('Du ', DATE_FORMAT(periode.periodeDateDebut,'%d/%m/%Y'),' au ',DATE_FORMAT(periode.periodeDateFin,'%d/%m/%Y')) AS 'periodeDates',
                    CONCAT('<div style=\'background-color: ',periode.periodeCouleur,';border: 2px solid black;width: 30px;\'>&nbsp;</div>') AS 'periodeCouleur'
                FROM periode";
        $dataList = new DataList($sql);
        $dataList->setOrderBy(['periodeDateDebut' => 'ASC']);
        $dataList->setColumn("periodeCouleur","");
        $dataList->setColumn("periodeLibelle","Titre");
        $dataList->setColumn("periodeDates","Dates", DataList::TYPE_DATE);
        $dataList->setColumn("periodeActif","Actif",DataList::TYPE_BOOL);
        $dataList->setOnClickParams('periodeId');

        break;
    case "site":
        $title = "Liste des sites de réservation";

        $sql = "SELECT *, CONCAT(site.siteCommission,'%') AS 'siteCommission',
                    CONCAT('<div style=\'background-color: ',site.siteCouleur,';border: 2px solid black;width: 30px;\'>&nbsp;</div>') AS 'siteCouleur'
                FROM site";
        $dataList = new DataList($sql);
        $dataList->setOrderBy(['siteLibelle' => 'ASC']);
        $dataList->setColumn("siteCouleur","");
        $dataList->setColumn("siteLibelle","Titre");
        $dataList->setColumn("siteCommission","Commission");
        $dataList->setColumn("siteActif","Actif",DataList::TYPE_BOOL);
        $dataList->setOnClickParams('siteId');

        break;
    default:
        Log::alert("Une erreur est survenue ! Le type de paramétrage n'est pas renseigné");
		exit();
		break;
}

$dataList->setTableCss('table table-light table-hover border');
$dataList->setTableTdCss('pointer');
$dataList->setOnClick('addInfo');
$dataList->setId('table-info');
?>

<script>
	function addInfo(id) {
	    let url = "ajax.php?do=php/parametrage&action=ajout";
            let fd = new FormData();
            fd.append('id',id);
            fd.append('table','<?= $table; ?>');
            popup(url, fd);
      }
</script>
<div class="row">
	<div class="col-12 mb-3">
		<b><?= $title; ?></b>
		<button class="btn btn-outline-success float-right" onclick="addInfo(0)">
			<span>
				<i class="fa fa-plus pr-1"></i>&nbsp;Ajouter
			</span>
		</button>
	</div>
	<div class="col-12" id="div-info">
		<?php
		$dataList->show();
		?>
	</div>
</div>