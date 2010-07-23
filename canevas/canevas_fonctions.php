<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function puce_statut_canevas_dist($id_canevas, $statut, $id_rubrique, $type, $ajax=''){
	if ($statut=='public') {
		$puce='puce-verte.gif';
	}
	else if ($statut == 'prive') {
		$puce = 'puce-orange.gif';
	}
	else
		$puce = 'puce-orange.gif';

	return http_img_pack($puce, $statut, "class='puce'");
}

?>
