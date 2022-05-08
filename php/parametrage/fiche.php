<script>
    $(function() {
        getPage('<?= $_SESSION[getSessionName()]['settingsTable'] ?? 'bien'; ?>');
    });
    function getPage(table) {
        let url = "ajax.php?do=php/parametrage&action=liste";
        let fd = new FormData();
        fd.append('table',table);
        ajaxCall(url, fd, (response) => {
                $('#div-list-info').html(response);
        });
    }
</script>

<h2 class="p-3 text-project-color ">
	<i class="fa fa-cog"></i>
	Paramétrages
</h2>
<div class="row m-2">
	<div class="col-12 col-lg-4 mt-2">
		<div class="card">
			<div class="card-body">
				<?php
				$sql = "SELECT *
						 FROM lien 
						 WHERE lien.lienType IS NOT NULL 
							AND lien.lienActif = 1";

				$dataList = new DataList($sql);
				$dataList->setOrderBy(['lienLibelle' => 'ASC']);

				$dataList->setColumn("lienLibelle", "Titre");
				$dataList->setColumn("lienActif", "Actif", DataList::TYPE_BOOL);

				$dataList->setTableCss('table table-light table-hover border');
				$dataList->setTableTdCss('pointer');

				$dataList->setOnClick('getPage');
				$dataList->setOnClickParams('lienType');

				$dataList->show();
				?>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-8 mt-2">
		<div class="card">
			<div class="card-body"  id="div-list-info"></div>
		</div>
	</div>
</div>