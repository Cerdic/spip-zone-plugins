<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_spipimmo_negociateur_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		spip_log("action_supprimer_spipimmo_negociateur_dist $arg pas compris");
	} else {
		action_supprimer_spipimmo_negociateur_post($r[1]);
	}
}

function action_supprimer_spipimmo_negociateur_post($id_negociateur) {
	sql_delete("spip_spipimmo_negociateurs", "id_negociateur=" . sql_quote($id_negociateur));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_negociateur/$id_negociateur'");
}
?>
