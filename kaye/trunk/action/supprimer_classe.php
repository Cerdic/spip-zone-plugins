<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_classe_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_classe_dist $arg pas compris");
	} else {
		action_supprimer_classe_post($r[1]);
	}
}

function action_supprimer_classe_post($id_classe) {
	sql_delete("spip_classes", "id_classe=" . sql_quote($id_classe));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_classe/$id_classe'");
}
?>
