<?php
$todo = readRequestVar('todo');

switch ($todo) {
    case 'getInfosBien':
        $data = readRequestVar('DATA');
        $tarif = readRequestVar('TARIF');
        $initData = readRequestVar('initData', false);
        $onlyTotal = readRequestVar('onlyTotal', false);
        $tarifTypeId = readRequestVar('tarifTypeId', 0);

        $bienId = $data['bienId'];
        $siteId = $data['siteId'];
        $dateDebut = $data['locationDateDebut'];
        $dateFin = $data['locationDateFin'];

        $toReturn = [];

        // Récupération des informations du bien
        $sql = "SELECT *
                FROM bien
                WHERE bien.bienId = :bienId";
        $res = DB::query($sql, [':bienId' => $bienId]);
        $row = $res->fetch(PDO::FETCH_ASSOC);

        $toReturn['bien'] = $row;

        // Récupération des informations du site
        $sql = "SELECT *
                FROM site
                WHERE site.siteId = :siteId";
        $res = DB::query($sql, [':siteId' => $siteId]);
        $row = $res->fetch(PDO::FETCH_ASSOC);

        $toReturn['site'] = $row;

        // Récupération des périodes par rapport aux dates
        $toReturn['periodes'] = [];

        $sql = "SELECT *
                FROM periode
                WHERE periode.periodeActif = 1
                    AND (
                        (
                            periode.periodeDateDebut <= :dateDebut AND
                            periode.periodeDateFin >= :dateFin
                        ) OR 
                        (
                            periode.periodeDateDebut <= :dateDebut AND 
                            periode.periodeDateFin >= :dateDebut
                        ) OR 
                        (
                            periode.periodeDateDebut >= :dateDebut AND 
                            periode.periodeDateDebut <= :dateFin
                        )
                    )";
        $res = DB::query($sql, [':dateDebut' => $dateDebut, ':dateFin' => $dateFin]);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $toReturn['periodes'][$row['periodeDateDebut']] = $row['periodeId'];
        }

        $nbNuit = 0;

        if (!$initData) {
            // Prix de base
            $prixBase = 0;
            // Prix du ménage
            $prixMenage = 0;
        } else {
            // Prix de base
            $prixBase = $tarif[TARIF_BASE]['tarifMontant'];
            // Prix du ménage
            $prixMenage = $tarif[TARIF_MENAGE]['tarifMontant'];
        }

        if (count($toReturn['periodes']) > 0) {
            // Récupération des tarifs
            $sql = "SELECT *
                    FROM bien_tarif
                    WHERE bien_tarif.bienId = :bienId
                    AND bien_tarif.periodeId IN(".implode(',', $toReturn['periodes']).")";
            $res = DB::query($sql, [':bienId' => $bienId]);
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $toReturn['tarifs'][$row['periodeId']][$row['tarifTypeId']] = $row;
            }

            $debut = new DateTime($dateDebut);
            $fin = new DateTime($dateFin);

            while ($debut < $fin) {
                $periodeId = "";
                foreach ($toReturn['periodes'] as $date => $id) {
                    if (empty($periodeId) || $debut->format('Y-m-d') >= $date) {
                        $periodeId = $id;
                    }
                }

                if (!$initData) {
                    $tarifs = $toReturn['tarifs'][$periodeId];
                    if (isset($tarifs[TARIF_BIEN_NUIT])) {
                        $prixBase += floatval($tarifs[TARIF_BIEN_NUIT]['bienTarifMontant']);
                    }
                    if (isset($tarifs[TARIF_BIEN_MENAGE])) {
                        $prixMenage = $tarifs[TARIF_BIEN_MENAGE]['bienTarifMontant'];
                    }
                }

                $debut->add(date_interval_create_from_date_string('1 day'));
                $nbNuit++;
            }
        }

        $toReturn['prixBase'] = $prixBase;
        $toReturn['prixMenage'] = $prixMenage;

        // Frais du site
        if ($tarifTypeId != TARIF_FRAIS_SITE && !$onlyTotal) {
            $infos = [
                'prixBase' => $toReturn['prixBase'],
                'prixMenage' => $toReturn['prixMenage'],
                'siteCommission' => $toReturn['site']['siteCommission'],
            ];
            $toReturn['fraisSite'] = getTarifLocation('fraisSite', $infos);
        } else {
            $toReturn['fraisSite'] = $tarif[TARIF_FRAIS_SITE]['tarifMontant'];
        }

        //Total perçu
        $infos = [
            'prixBase' => $toReturn['prixBase'],
            'prixMenage' => $toReturn['prixMenage'],
            'fraisSite' => $toReturn['fraisSite'],
        ];
        $toReturn['totalPercu'] = getTarifLocation('totalPercu', $infos);

        // Frais Dracénie BNB
        if ($tarifTypeId != TARIF_FRAIS_DRACENIE  && !$onlyTotal) {
            $infos = [
                'totalPercu' => $toReturn['totalPercu'],
                'bienCommission' => $toReturn['bien']['bienCommission'],
            ];
            $toReturn['fraisDracenie'] = getTarifLocation('fraisDracenie', $infos);
        } else {
            $toReturn['fraisDracenie'] = $tarif[TARIF_FRAIS_DRACENIE]['tarifMontant'];
        }

        // Perçu Dracenie
        $infos = [
            'fraisDracenie' => $toReturn['fraisDracenie'],
            'prixMenage' => $toReturn['prixMenage'],
            'tarifMontant' => $data['TARIF'][TARIF_FRAIS_PARTENAIRE]['tarifMontant'],
        ];
        $toReturn['totalPercuDracenie'] = getTarifLocation('totalPercuDracenie', $infos);

        // Total client
        $infos = [
            'totalPercu' => $toReturn['totalPercu'],
            'totalPercuDracenie' => $toReturn['totalPercuDracenie'],
            'tarifMontant' => $data['TARIF'][TARIF_FRAIS_PARTENAIRE]['tarifMontant'],
        ];
        $toReturn['totalClient'] = getTarifLocation('totalClient', $infos);

        // Perçu BRUT
        $infos = [
            'totalPercu' => $toReturn['totalPercu'],
            'totalClient' => $toReturn['totalClient'],
        ];
        $toReturn['totalPercuBrut'] = getTarifLocation('totalPercuBrut', $infos);

        // Nombre de nuit
        $toReturn['nbNuit'] = $nbNuit;

        // Tarif par nuit
        $infos = [
            'totalPercu' => $toReturn['totalPercu'],
            'nbNuit' => $toReturn['nbNuit'],
        ];
        $toReturn['totalTarifNuit'] = getTarifLocation('totalTarifNuit', $infos);

        // Formatage des totaux
        $toReturn['totalPercu'] = number_format($toReturn['totalPercu'],2,',',' ');
        $toReturn['site']['siteCommission'] = number_format($toReturn['site']['siteCommission'],2,',',' ').'%';

        echo json_encode($toReturn);

        break;
    case 'calculInfosBien':

        break;
}