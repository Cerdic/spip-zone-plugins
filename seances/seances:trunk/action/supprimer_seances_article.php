<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_seances_article_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_seances_article_dist $arg pas compris");
	} else {
		action_supprimer_seances_article_post($r[1]);
	}
}

function action_supprimer_seances_article_post($id_article) {
	sql_delete('spip_seances', 'id_article='.sql_quote($id_article));
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_article'");
}
?>