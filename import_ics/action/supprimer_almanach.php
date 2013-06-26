<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_almanach_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_almanach_dist $arg pas compris");
	} else {
		action_supprimer_almanach_post($r[1]);
	}
}

function action_supprimer_almanach_post($id_almanach) {
	sql_delete("spip_almanachs", "id_almanach=" . sql_quote($id_almanach));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_almanach/$id_almanach'");
}
?>