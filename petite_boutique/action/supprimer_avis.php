<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_avis_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_avis_dist $arg pas compris");
	} else {
		action_supprimer_avis_post($r[1]);
	}
}

function action_supprimer_avis_post($id_avis) {
	sql_delete("spip_avis_boutique", "id_avis=" . sql_quote($id_avis));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_avis/$id_avis'");
}
?>
