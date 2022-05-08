<?php
$tarifTypes = [];
$locations = [];

$sql = "SELECT *
        FROM tarif_type
        WHERE tarif_type.tarifTypeActif = 1
            AND tarif_type.tarifTypeObjetTable LIKE 'location'";
$res = DB::query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $tarifTypes[$row['tarifTypeId']] = $row;
}

$sql = "SELECT *
        FROM location
        WHERE 1=1";
$res = DB::query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $locations[$row['locationId']] = $row;
}


p($tarifTypes);
p($locations);

?>