<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de renouveler un abonnement
 * @param int $arg
 * @return unknown_type
 */
function action_renouveler_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// Si on a bien un abonnement et qu'on a le droit de le modifier
	if (
		$id_abonnement = intval($arg)
		and autoriser('modifier', 'abonnement', $id_abonnement)
		and $abonnement = sql_fetsel('id_abonnements_offre, date_debut, date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement)
		and $offre = sql_fetsel('duree, periode', 'spip_abonnements_offres', 'id_abonnements_offre = '.$abonnement['id_abonnements_offre'])
		and $offre['duree'] > 0
		and $offre['periode']
	) {
		$action = charger_fonction('modifier_echeance_abonnement', 'action/');
		return $action($id_abonnement.'/'.$offre['duree'].'/'.$offre['periode']);
	}
	
	return false;
}

