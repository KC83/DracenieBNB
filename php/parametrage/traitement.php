<?php
$todo = $_REQUEST['todo'] ?? null;

switch ($todo) {
	case 'save':
		$id = $_REQUEST['id'] ?? 0;
		$table = $_REQUEST['table'] ?? '';
		$nameId = $_REQUEST['nameId'] ?? '';

		$data = $_REQUEST['DATA'];
		$id = DB::updateDefault($id,$table,$nameId,$data);

        if ($table == 'bien') {
            $tarifs = readRequestVar('TARIF');
            $toKeep = [];

            foreach ($tarifs as $periodeId => $items) {
                foreach ($items as $tarifTypeId => $tarif) {
                    if (empty($tarif['bienTarifMontant'])) {
                        continue;
                    }

                    $tarif['bienId'] = $id;
                    $tarif['tarifTypeId'] = $tarifTypeId;
                    $tarif['periodeId'] = $periodeId;

                    $bienTarifId = DB::updateDefault($tarif['bienTarifId'],'bien_tarif','bienTarifId',$tarif);
                    $toKeep[] = $bienTarifId;
                }
            }

            $sql = "DELETE FROM bien_tarif
                    WHERE bien_tarif.bienId = :bienId";
            if (count($toKeep) > 0) {
                $sql .= " AND bien_tarif.bienTarifId NOT IN(".implode(',', $toKeep).")";
            }
            DB::query($sql, [':bienId' => $id]);
        }

        echo $id;

		break;
}