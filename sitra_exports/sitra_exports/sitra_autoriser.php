<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, rien a effectuer
function sitra_autoriser(){}


// pour la remise à zéro
function autoriser_sitra_administrer($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo');
}

function autoriser_sitra_bouton_dist($faire, $type, $id, $qui, $opt) {
    return autoriser('administrer', 'sitra', $id, $qui, $opt);
}
?>