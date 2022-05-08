<?php
$locationId = readRequestVar('locationId');

$row = new stdClass();

$title = "Ajout d'une location";
if ($locationId > 0) {
    $title = "Modification d'une location";

    $sql = "SELECT *
            FROM location
                LEFT OUTER JOIN locataire ON locataire.locataireId = location.locataireId
            WHERE location.locataireId = :locationId";
    $res = DB::query($sql, [':locationId' => $locationId]);
    $row = $res->fetchObject();
}

$tarifs = [];
$tarifTypes = [];

$sql = "SELECT *
        FROM tarif_type
        WHERE tarif_type.tarifTypeActif = 1
            AND tarif_type.tarifTypeObjetTable LIKE 'location'";
$res = DB::query($sql);
while ($tarifType = $res->fetch(PDO::FETCH_ASSOC)) {
    $tarifTypes[$tarifType['tarifTypeId']] = $tarifType;
}

?>

<script>
    function save(sender) {
        if (!validateForm('formLocation')) {
            return false;
        }

        let oldHtml = sender.innerHTML;
        sender.innerHTML = '<span><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>';
        sender.disabled = true;

        let url = "ajax.php?do=php/location&action=traitement&todo=save";
        let fd = generateFormData('formLocation');

        ajaxCall(url, fd, (response) => {
            if (response > 0) {
                redirect('index.php?do=php/location&action=ajout&locationId='+response);
                return false;
            } else {
                alerte("Une erreur est survenue !",'Fermer');
                console.log(response);
            }

            sender.innerHTML = oldHtml;
            sender.disabled = false;
            return false;
        });

        return false;
    }
    function getInfosBien(initData = 0, tarifTypeId = 0, onlyTotal = 0) {
        let url = "ajax.php?do=php/location/ajax&action=general&todo=getInfosBien";
        let fd = generateFormData('formLocation');
        fd.append('initData',initData);
        fd.append('tarifTypeId',tarifTypeId);
        fd.append('onlyTotal',onlyTotal);

        ajaxCall(url, fd, function (response) {
            let json = JSON.parse(response);

            $('#tarif<?= TARIF_BASE; ?>').val(json.prixBase);
            $('#tarif<?= TARIF_MENAGE; ?>').val(json.prixMenage);
            $('#tarif<?= TARIF_FRAIS_SITE; ?>').val(json.fraisSite);
            $('#tarif<?= TARIF_FRAIS_DRACENIE; ?>').val(json.fraisDracenie);
            $('#tarif<?= TARIF_FRAIS_MENAGE_DRACENIE; ?>').val(json.prixMenage);

            $('#totalPercu').html(json.totalPercu);
            $('#totalClient').html(json.totalClient);
            $('#totalPercuBrut').html(json.totalPercuBrut);
            $('#totalPercuDracenie').html(json.totalPercuDracenie);
            $('#totalTarifNuit').html(json.totalTarifNuit);
            $('#totalCommissionSite').html(json.site.siteCommission);
        })
    }
    function confirmGetInfosBien(initData, tarifTypeId) {
        confirme("Recalculer tous les champs ?", function () {
            getInfosBien(initData, tarifTypeId);
        }, function () {
            getInfosBien(initData, tarifTypeId,1);
        },'Confirmation','Oui','Non');
    }
</script>

<form id="formLocation" class="pt-3" method="post">
    <input type="hidden" name="locationId" id="locationId" value="<?= $locationId; ?>">
    <input type="hidden" name="locataireId" id="locataireId" value="<?= $row->locataireId ?? null; ?>">

    <div class="row ml-2 mr-2">
        <div class="col-12 col-md-6 pt-3">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Informations générales
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="bienId" class="font-weight-bold">Bien</label>
                        <select class="form-control" name="DATA[bienId]" id="bienId" onchange="getInfosBien()" required>
                            <?= createOption($row->bienId ?? 0,'bien','bien'); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="siteId" class="font-weight-bold">Site de réservation</label>
                        <select class="form-control" name="DATA[siteId]" id="siteId" required>
                            <?= createOption($row->siteId ?? 0,'site','site'); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="locationDateDebut" class="font-weight-bold">Date de début</label>
                        <input type="date" class="form-control" name="DATA[locationDateDebut]" id="locationDateDebut" value="<?= $row->locationDateDebut ?? null; ?>" onchange="getInfosBien()" onkeyup="getInfosBien()" required>
                    </div>
                    <div class="form-group">
                        <label for="locationDateFin" class="font-weight-bold">Date de fin</label>
                        <input type="date" class="form-control" name="DATA[locationDateFin]" id="locationDateFin" value="<?= $row->locationDateFin ?? null; ?>" onchange="getInfosBien()" onkeyup="getInfosBien()" required>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="locationNbAdulte" class="font-weight-bold">Nombre d'adultes</label>
                                <input type="number" step="1" class="form-control" name="DATA[locationNbAdulte]" id="locationNbAdulte" value="<?= $row->locationNbAdulte ?? 0; ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="locationNbEnfant" class="font-weight-bold">Nombre d'enfants</label>
                                <input type="number" step="1" class="form-control" name="DATA[locationNbEnfant]" id="locationNbEnfant" value="<?= $row->locationNbEnfant ?? 0; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 pt-3">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Locataire
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="locataireNom" class="font-weight-bold">Nom de famille</label>
                        <input type="text" class="form-control" name="LOCATAIRE[locataireNom]" id="locataireNom" value="<?= $row->locataireNom ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="locatairePrenom" class="font-weight-bold">Prénom</label>
                        <input type="text" class="form-control" name="LOCATAIRE[locatairePrenom]" id="locatairePrenom" value="<?= $row->locatairePrenom ?? null; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="locataireTelephone" class="font-weight-bold">Téléphone</label>
                        <div class="input-group">
                            <input type="tel" class="form-control" name="LOCATAIRE[locataireTelephone]" id="locataireTelephone" value="<?= $row->locataireTelephone ?? null; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="locataireEmail" class="font-weight-bold">E-mail</label>
                        <div class="input-group">
                            <input type="email" class="form-control" name="LOCATAIRE[locataireEmail]" id="locataireEmail" value="<?= $row->locataireEmail ?? null; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-at"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="locatairePays" class="font-weight-bold">Pays</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="LOCATAIRE[locatairePays]" id="locatairePays" value="<?= $row->locatairePays ?? null; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa-solid fa-globe"></i></span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 pt-3">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Tarifs
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 pt-3">
                            <div class="row">
                                <?php foreach ($tarifTypes as $tarifTypeId => $tarifType) { ?>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label for="tarif<?= $tarifTypeId; ?>" class="font-weight-bold"><?= $tarifType['tarifTypeLibelle']; ?></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" name="TARIF[<?= $tarifTypeId; ?>][tarifMontant]" id="tarif<?= $tarifTypeId; ?>" value="" onblur="confirmGetInfosBien(1, '<?= $tarifTypeId; ?>')">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-euro-sign"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 pt-3">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Total perçu : </label>
                                        <span id="totalPercu">0</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Total client : </label>
                                        <span id="totalClient">0</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Total perçu BRUT : </label>
                                        <span id="totalPercuBrut">0</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Total perçu Dracénie : </label>
                                        <span id="totalPercuDracenie">0</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tarif/Nuit : </label>
                                        <span id="totalTarifNuit">0</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Commission du site : </label>
                                        <span id="totalCommissionSite">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 pt-3">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Commentaire
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea class="form-control" rows="4" name="DATA[locationCommentaire]" id="locationCommentaire"><?= $row->locationCommentaire ?? null; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 pt-3 text-center">
            <button class="btn btn-success" type="button" onclick="save(this)">
                <i class="fa-solid fa-save"></i>
                <span>Enregistrer</span>
            </button>

            <?php if ($locationId > 0) { ?>
                <button class="btn btn-danger" type="button" onclick="deleteLocation(this)">
                    <i class="fa-solid fa-trash"></i>
                    <span>Supprimer</span>
                </button>
            <?php } ?>
        </div>
    </div>
</form>

