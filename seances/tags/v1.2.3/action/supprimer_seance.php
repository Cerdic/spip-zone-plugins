<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_seance_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_seance_dist $arg pas compris");
	} else {
		action_supprimer_seance_post($r[1]);
	}
}

function action_supprimer_seance_post($id_seance) {
	sql_delete('spip_seances', 'id_seance='.sql_quote($id_seance));
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_seance/$id_seance'");
}
?>