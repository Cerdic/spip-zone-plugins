<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_banniere_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_banniere_dist $arg pas compris");
	} else {
		action_supprimer_banniere_post($r[1]);
	}
}

function action_supprimer_banniere_post($id_banniere) {
	sql_delete("spip_bannieres", "id_banniere=" . sql_quote($id_banniere));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_banniere/$id_banniere'");
}
?>
