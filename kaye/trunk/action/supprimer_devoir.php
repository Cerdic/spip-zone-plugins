<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_devoir_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_devoir_dist $arg pas compris");
	} else {
		action_supprimer_devoir_post($r[1]);
	}
}

function action_supprimer_devoir_post($id_devoir) {
	sql_delete("spip_devoirs", "id_devoir=" . sql_quote($id_devoir));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_devoir/$id_devoir'");
}
?>
