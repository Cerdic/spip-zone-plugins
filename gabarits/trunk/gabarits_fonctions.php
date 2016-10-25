<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function puce_statut_gabarit_dist($id_gabarit, $statut, $id_rubrique, $type, $ajax='') {
	if ($statut=='public') {
		$puce='puce-verte.gif';
	}
	else if ($statut == 'prive') {
		$puce = 'puce-orange.gif';
	} else {
		$puce = 'puce-orange.gif';
	}
	return http_img_pack($puce, $statut, "class='puce'");
}
