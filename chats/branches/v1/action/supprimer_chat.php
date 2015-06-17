<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_chat_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		spip_log("action_supprimer_chat_dist $arg pas compris");
	} else {
		action_supprimer_chat_post($r[1]);
	}
}

function action_supprimer_chat_post($id_chat) {
	sql_delete("spip_chats", "id_chat=" . sql_quote($id_chat));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_chat/$id_chat'");
}
