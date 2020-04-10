<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Vérifie si les abonnements actifs ont bien une tâche précise de désactivation
 */
function genie_abonnements_verifier_desactivation_dist($time){
	include_spip('base/abstract_sql');
	
	// On va chercher tous les abonnements actifs + ayant une date de fin + sans job lié
	if ($a_changer = sql_allfetsel(
		'id_abonnement, date_fin',
		'spip_abonnements as a'.
			' LEFT JOIN spip_jobs_liens AS l ON l.objet = "abonnement" AND l.id_objet = a.id_abonnement'.
			' LEFT JOIN spip_jobs AS j ON j.fonction = "abonnements_desactiver" AND j.id_job = l.id_job',
		array(
			'a.statut = "actif"',
			'a.date_fin > ' . sql_quote('0000-00-00 00:00:00'),
			'l.id_job IS NULL',
		)
	) and is_array($a_changer)) {
		include_spip('inc/abonnements');
		foreach ($a_changer as $abonnement){
			abonnements_programmer_desactivation($abonnement['id_abonnement'], $abonnement['date_fin']);
		}
	}
	
	return 1;
}

?>
