<?php

/**
 * Plugin abonnements pour Spip 2.0
 * Licence GPL (c) 2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_supprimer_abonnement_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_abonnement_dist $arg pas compris");
	} else action_supprimer_abonnement_post($r[1]);
}


function action_supprimer_abonnement_post($id_abonnement)
{
	if ($id_abonnement < 0){
		sql_delete("spip_abonnements", "id_abonnement=" . (0- $id_abonnement));
	}
	else
		spip_log('appel deprecie, rien a faire ici (voir action/editer_abonnement)');
}


?>
