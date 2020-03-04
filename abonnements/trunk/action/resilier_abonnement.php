<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_resilier_abonnement($id_abonnement=null) {
	if (is_null($id_abonnement)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_abonnement = $securiser_action();
	}
	
	if (
		$id_abonnement = intval($id_abonnement)
		and $id_abonnement > 0
		and include_spip('inc/autoriser')
		and autoriser('resilier', 'abonnement', $id_abonnement)
	) {
		include_spip('action/editer_objet');
		include_spip('inc/abonnements');
		
		// L'abonnement SPIP sera désactivé à la fin de l'échéance, on force donc cette date de fin, et on reprogramme dès maintenant la désactivation
		$date_echeance = sql_getfetsel('date_echeance', 'spip_abonnements', 'id_abonnement = '.$id_abonnement);
		objet_modifier('abonnement', $id_abonnement, array(
			'date_fin' => $date_echeance,
		));
		abonnements_programmer_desactivation($id_abonnement, $date_echeance);
		
		// Si on détecte qu'il est lié à un prélèvement bancaire, on lance une résiliation par l'API
		if (
			defined('_DIR_PLUGIN_COMMANDES')
			and defined('_DIR_PLUGIN_BANK')
			and include_spip('action/editer_liens')
			and $liens = objet_trouver_liens(array('commande' => '*'), array('abonnement' => $id_abonnement))
			and is_array($liens)
			// On prend juste la première commande qu'on trouve
			and $id_commande = intval($lien_commande[0]['id_commande'])
			and $bank_uid = sql_getfetsel('bank_uid', 'spip_commandes', 'id_commande = '.$id_commande)
		) {
			include_spip('abos/resilier');
			abos_resilier_notify_bank($bank_uid);
		}
	}
}
