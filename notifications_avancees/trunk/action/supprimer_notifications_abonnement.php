<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action d'activation / désactivation d'un abonnement
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_supprimer_notifications_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// On ne fait quelque chose que si l'identifiant est correct
	// et que l'abonnement est actif
	if ($id_notifications_abonnement = intval($arg)
		and $id_notifications_abonnement = intval(sql_getfetsel('id_notifications_abonnement', 'spip_notifications_abonnements', 'id_notifications_abonnement = '.$id_notifications_abonnement))
	){
		sql_delete(
			'spip_notifications_abonnements',
			'id_notifications_abonnement = '.$id_notifications_abonnement
		);
	}
}

?>
