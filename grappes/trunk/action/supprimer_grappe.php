<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de suppression d'une grappe
 * 
 * Doit recevoir comme argument (arg) "-#ID_GRAPPE" les "-" étant obligatoire
 */
function action_supprimer_grappe_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_grappe_dist $arg pas compris");
	} else action_supprimer_grappe_post($r[1]);
}

/**
 * Suppression d'une grappe
 * 
 * @param int $id_grappe
 * 	Identifiant de la grappe à supprimer avec un "-"
 * @return
 */
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