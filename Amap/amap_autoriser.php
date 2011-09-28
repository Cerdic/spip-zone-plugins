<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function amap_autoriser(){}

function autoriser_amap_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo');
}
?>
