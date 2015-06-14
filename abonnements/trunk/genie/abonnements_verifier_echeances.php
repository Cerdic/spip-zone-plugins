<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérifier si des échéances sont dépassées
 **/
function genie_abonnements_verifier_echeances_dist($time){
	include_spip('base/abstract_sql');
	include_spip('inc/config');
	$jourdhui = date('Y-m-d H:I:s');
	$heures_max_retard = lire_config('abonnements/echeance_heures_limite', 48); // 48h par défaut
	$date_max_retard = date('Y-m-d H:i:s', strtotime('-'.$heures_max_retard.'hours'));
	
	// On va chercher tous les abonnements sans fin ou avec fin future,
	// dont la dernière échéance est TROP dépassée (48h par défaut, configurable)
	if (
		$abonnements_retard = sql_allfetsel(
			'id_abonnement, date_fin',
			'spip_abonnements',
			array(
				"date_fin = '0000-00-00 00:00:00' or date_fin > '$jourdhui'",
				"date_echeance < '$date_max_retard'",
			)
		)
		and is_array($abonnements_retard)
	) {
		include_spip('action/editer_objet');
		
		// Pour chaque abonnement trop dépassé, on met la date de fin à maintenant
		// ce qui va normalement provoquer la désactivation immédiate
		foreach ($abonnements_retard as $abonnement) {
			objet_modifier('abonnement', $abonnement['id_abonnement'], array('date_fin' => $jourdhui));
		}
	}
	
	return 1;
}
