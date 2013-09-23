<?php
/**
* @plugin	Amap
* @author	Stephane Moulinet
* @author	E-cosystems
* @author	Pierre KUHN 
* @copyright 2010-2013
* @licence	GNU/GPL
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_amap_panier_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		spip_log("action_supprimer_amap_panier_dist $arg pas compris");
	} else {
		action_supprimer_amap_panier_post($r[1]);
	}
}

function action_supprimer_amap_panier_post($id_amap_panier) {
	sql_delete("spip_amap_paniers", "id_amap_panier=" . sql_quote($id_amap_panier));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_amap_panier/$id_amap_panier'");
}
?>
