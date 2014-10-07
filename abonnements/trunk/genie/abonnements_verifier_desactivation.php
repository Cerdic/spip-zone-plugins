<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Vérifie si les abonnements actifs ont bien une tâche précise de désactivation
 */
function genie_abonnements_verifier_desactivation_dist($time){
	include_spip('base/abstract_sql');
	$jourdhui = date('Y-m-d H:I:s');
	
	// On va chercher tous les abonnements actifs + ayant une date de fin future + sans job lié
	if ($a_changer = sql_allfetsel(
		'id_abonnement, date_fin',
		'spip_abonnements as a left join spip_jobs_liens as l on l.objet="abonnement" and l.id_objet=a.id_abonnement left join spip_jobs as j on j.fonction="abonnements_desactiver" and j.id_job=l.id_job',
		array(
			'a.statut = "actif"',
			'a.date_fin > '.sql_quote($jourdhui),
			'l.id_job is null',
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
