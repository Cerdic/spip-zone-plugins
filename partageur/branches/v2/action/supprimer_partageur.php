<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_partageur_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_partageur_dist $arg pas compris");
	} else {
		action_supprimer_partageur_post($r[1]);
	}
}

function action_supprimer_partageur_post($id_partageur) {
	sql_delete("spip_partageurs", "id_partageur=" . sql_quote($id_partageur));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_partageur/$id_partageur'");
}
?>
