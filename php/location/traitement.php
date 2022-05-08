<?php
$todo = readRequestVar('todo');

switch ($todo) {
    case 'save':
        $locationId = readRequestVar('locationId',0);
        $locataireId = readRequestVar('locataireId',0);
        $data = readRequestVar('DATA');
        $locataire = readRequestVar('LOCATAIRE');

        //Vérification si le bien est disponible pour cette date
        if (!isBienDisponible($data['bienId'], $data['locationDateDebut'], $data['locationDateFin'])) {
            echo "Le bien n'est pas disponible pour ces dates !";
            exit();
        }

        // Enregistrement du locataire
        $locataireId = DB::updateDefault($locataireId, 'locataire', 'locataireId',$locataire);
        $data['locataireId'] = $locataireId;

        // Enregistrement de la location
        $locationId = DB::updateDefault($locationId, 'location', 'locationId',$data);

        echo $locationId;

        break;
}