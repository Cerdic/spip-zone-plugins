<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_relance_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_relance_dist $arg pas compris");
	} else {
		action_supprimer_relance_post($r[1]);
	}
}

function action_supprimer_relance_post($id_relance) {
	sql_delete("spip_relances", "id_relance=" . sql_quote($id_relance));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_relance/$id_relance'");
}
?>
