<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_commune_lien_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)-([a-z_]*)-(\d+)$,", $arg, $r)) {
		spip_log("action_supprimer_commune_lien_dist $arg pas compris");
	}
	else {
		action_supprimer_commune_lien_post($r[1],$r[2],$r[3]);
		}

}

function action_supprimer_commune_lien_post($id_cog_commune,$objet,$id_objet) {
	sql_delete("spip_cog_communes_liens", "id_cog_commune=".sql_quote($id_cog_commune)." and objet=".sql_quote($objet)." and id_objet=" .sql_quote($id_objet) );
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_$objet/$id_objet'");
}
?>
