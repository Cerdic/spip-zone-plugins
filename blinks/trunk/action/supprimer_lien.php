<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function action_supprimer_lien_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_lien_dist $arg pas compris");
	} else {
		action_supprimer_lien_post($r[1]);
	}
}
function action_supprimer_lien_post($id_blink) {
	sql_delete("spip_blinks", "id_blink=" . sql_quote($id_blink));
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_blink/$id_blink'");
}
?>