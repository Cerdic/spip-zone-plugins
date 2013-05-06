<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_seances_endroit_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_seances_endroit_dist $arg pas compris");
	} else {
		action_supprimer_seances_endroit_post($r[1]);
	}
}

function action_supprimer_seances_endroit_post($id_endroit) {
	sql_delete('spip_seances_endroits', 'id_endroit='.sql_quote($id_endroit));
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_endroit/$id_endroit'");
}
?>