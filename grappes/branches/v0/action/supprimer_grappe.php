<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// https://code.spip.net/@action_instituer_groupe_mots_dist
function action_supprimer_grappe_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_grappe_dist $arg pas compris");
	} else action_supprimer_grappe_post($r[1]);
}

// https://code.spip.net/@action_instituer_groupe_mots_post
function action_supprimer_grappe_post($id_grappe)
{
	if ($id_grappe < 0){
		sql_delete("spip_grappes_liens", "id_grappe=" . (0- $id_grappe));
		sql_delete("spip_grappes", "id_grappe=" . (0- $id_grappe));
	}
	else
		spip_log('appel deprecie, rien a faire ici (voir action/editer_grappe)');

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_grappe/$id_grappe'");
}
?>