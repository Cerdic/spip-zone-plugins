<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function autoriser_amap_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo');
}
?>
