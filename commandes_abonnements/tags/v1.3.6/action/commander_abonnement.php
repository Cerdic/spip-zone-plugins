<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_commander_abonnement_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	$erreur = null;
	
	// Si on a bien une offre
	if (
		$id_abonnements_offre = intval($arg)
		and $offre = sql_fetsel('*', 'spip_abonnements_offres', 'id_abonnements_offre = '.$id_abonnements_offre)
	) {
		include_spip('inc/session');

		// Trouver le prix par défaut de l'offre demandée
		$trouver_prix = charger_fonction('prix', 'inc');
		$prix_defaut = $trouver_prix('abonnements_offre', $id_abonnements_offre);
		
		// On va enregistrer en session les infos nécessaires à la commande
		// elle sera créée quand on aura un utilisateur sous la main et qu'on sera sûr d'avoir ses infos à jour
		$commande_abonnement = array(
			'id_abonnements_offre' => $id_abonnements_offre,
			'montant' => $prix_defaut,
		);

		session_set('commande_abonnement', $commande_abonnement);
	}
	else {
		$erreur = 'L’offre demandée n’existe pas.';
	}

	return array($id_abonnements_offre, $erreur);
}
