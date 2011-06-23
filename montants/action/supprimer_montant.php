<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// http://doc.spip.org/@action_instituer_groupe_mots_dist
function action_supprimer_montant_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_montant_dist $arg pas compris");
	} else action_supprimer_montant_post($r[1]);
}

// http://doc.spip.org/@action_instituer_groupe_mots_post
function action_supprimer_montant_post($id_montant)
{
	if ($id_montant < 0){
		sql_delete("spip_montants_liens", "id_montant=" . (0- $id_montant));
		sql_delete("spip_montants", "id_montant=" . (0- $id_montant));
	}
	else
		spip_log('appel deprecie, rien a faire ici (voir action/editer_montant)');

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_montant/$id_montant'");
}
?>