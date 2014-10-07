<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Programmer la désactivation d'un abonnement lors de sa date de fin
 *
 * @param int $id_abonnement
 *	L'identifiant de l'abonnement
 * @param datetime $date_fin
 *	Optionnellement la date de fin si on la connait déjà, ce qui évite une requête
 */
function abonnements_programmer_desactivation($id_abonnement, $date_fin=null){
	include_spip('action/editer_liens');
	$id_abonnement = intval($id_abonnement);
	
	// Si on a pas de date, on va chercher
	if (!$date_fin){
		$date_fin = sql_getfetsel('date_fin', 'spip_abonnements', 'id_abonnement = '.$id_abonnement);
	}
	
	// Dans tous les cas on cherche s'il y des tâches liées à cet abonnement
	$liens = objet_trouver_liens(array('job' => '*'), array('abonnement' => $id_abonnement));
	if ($liens and is_array($liens)){
		// Et on les supprime toutes !
		foreach ($liens as $lien){
			job_queue_remove($lien['id_job']);
		}
	}
	
	// Seulement si on a bien une date de fin, on reprogramme, sans duplication possible
	if ($date_fin and $date_fin != '0000-00-00 00:00:00'){
		$id_job = job_queue_add(
			'abonnements_desactiver',
			_T('abonnement:job_desactivation', array('id'=>$id_abonnement)),
			array($id_abonnement),
			'inc/abonnements',
			true,
			strtotime($date_fin)
		);
		job_queue_link($id_job, array('objet'=>'abonnement', 'id_objet'=>$id_abonnement));
	}
}

/*
 * Désactiver un abonnement en utilisant l'API et sans autorisation
 */
function abonnements_desactiver($id_abonnement){
	include_spip('inc/autoriser');
	include_spip('action/editer_objet');
	// On inhibe les autorisations
	autoriser_exception('modifier', 'abonnement', $id_abonnement);
	autoriser_exception('instituer', 'abonnement', $id_abonnement);
	// On désactive l'abonnement
	objet_modifier('abonnement', $id_abonnement, array('statut' => 'inactif'));
	// On remet les autorisations
	autoriser_exception('instituer', 'abonnement', $id_abonnement, false);
	autoriser_exception('modifier', 'abonnement', $id_abonnement, false);
}

/*
 * Envoyer un courriel à l'abonné pour lui rappeler combien de temps il lui reste
 */
function abonnements_notifier_echeance($id_abonnement, $nom, $email, $duree, $periode){
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
	
	$envoyer_mail(
		$email,
		_T('abonnement:notification_echeance_sujet_'.$periode, array('duree'=>$duree)),
		recuperer_fond(
			'notifications/abonnement_echeance',
			array(
				'id_abonnement' => $id_abonnement,
				'nom' => $nom,
				'email' => $email,
				'duree' => $duree,
				'periode' => $periode
			)
		)
	);
}

?>
