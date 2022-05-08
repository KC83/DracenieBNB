<?php
function readRequestVar($column, $defaultValue = '') {
    if (isset($_REQUEST[$column]) && !empty($_REQUEST[$column])) {
        return $_REQUEST[$column];
    } else {
        return $defaultValue;
    }
}
function redirect($redirect) {
    header('Location: '.$redirect);
}

function dateSqlToFr($date): string {
	try {
		$date = new DateTime($date);
		return $date->format('d/m/Y');
	} catch (Exception $e) {
	}

	return '';
}
function dateFrToSql($date) {
	try {
		$date = new DateTime($date);
		return $date->format('Y-m-d');
	} catch (Exception $e) {
	}

	return '';
}

function getSessionName() {
	return $GLOBALS['config']['nameSession'];
}
function p($message) {
    echo "<pre>";
    print_r($message);
    echo "</pre>";
}

function createOption(int $id, string $table, string $tableName, ?string $orderBy = null, ?string $where = null) {
	$sql = "SELECT * FROM ".$table." WHERE IFNULL(".$tableName."Actif,0) = 1";
	if (!empty($where)) {
		$sql .= " ".$where;
	}

	if (empty($orderBy)) {
		$orderBy = $tableName."Libelle ASC";
	}
	$sql .= " ORDER BY ".$orderBy;


	$query = DB::query($sql, []);

	$toReturn = '<option value="">Choix</option>';
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$selected = '';
		if ($id == $row[$tableName.'Id']) {
			$selected = 'selected';
		}

		$toReturn .= '<option value="'.$row[$tableName.'Id'].'" '.$selected.'>'.$row[$tableName.'Libelle'].'</option>';
	}

	return $toReturn;
}

function getTarifLocation($tarifType, $infos) {
    $toReturn = 0;

    switch ($tarifType) {
        case 'fraisSite':
            if ($infos['siteCommission'] > 0) {
                $toReturn = round((($infos['prixBase']+$infos['prixMenage'])*$infos['siteCommission'])/100, 2);
            }
            break;
        case 'totalPercu':
            $toReturn = round($infos['prixBase']+$infos['prixMenage']-$infos['fraisSite'],2);
            break;
        case 'fraisDracenie':
            if ($infos['bienCommission'] > 0) {
                $toReturn = round(($infos['totalPercu']*$infos['bienCommission'])/100,2);
            }
            break;
        case 'totalPercuDracenie':
            $toReturn = round($infos['fraisDracenie']+$infos['prixMenage']-$infos['tarifMontant'],2);
            break;
        case 'totalClient':
            $toReturn = round($infos['totalPercu']-$infos['totalPercuDracenie']-$infos['tarifMontant'],2);
            break;
        case 'totalPercuBrut':
            $toReturn = round($infos['totalPercu']-$infos['totalClient']);
            break;
        case 'totalTarifNuit':
            if ($infos['nbNuit'] > 0) {
                $toReturn = number_format($infos['totalPercu']/$infos['nbNuit'],2,',',' ');
            }
            break;
    }

    return $toReturn;
}
function isBienDisponible($bienId, $dateDebut, $dateFin) {
    return true;
}