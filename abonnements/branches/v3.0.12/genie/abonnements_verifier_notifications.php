<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Vérifie si aujourd'hui on a des notifications à envoyer à certains abonnés
 */
function genie_abonnements_verifier_notifications_dist($time){
	include_spip('base/abstract_sql');
	$jourdhui = date('Y-m-d H:I:s');
	
	// On va chercher toutes les notifications
	if (
		$notifications = sql_allfetsel('*', 'spip_abonnements_offres_notifications')
		and is_array($notifications)
	){
		// Pour chaque notification on va chercher les abonnés dont c'est le moment
		foreach ($notifications as $notification){
			// De combien doit-on modifier la date
			switch ($notification['periode']){
				case 'jours':
					$ajout = " + ${notification['duree']} days";
					break;
				case 'mois':
					$ajout = " + ${notification['duree']} months";
					break;
				default:
					$ajout ='';
					break;
			}
			
			// S'il y a de quoi ajouter, alors on va calculer l'échéance à chercher
			if ($ajout){
				$echeance = date('Y-m-d', strtotime($jourdhui.$ajout));
			}
			
			// Pour cette notification on cherche donc tous les abonnés ayant cet échéance avec la même offre
			if ($a_notifier = sql_allfetsel(
				'id_abonnement, nom, email',
				'spip_abonnements as a left join spip_auteurs as u on a.id_auteur=u.id_auteur',
				array(
					'DATE_FORMAT(date_fin, "%Y-%m-%d") = '.sql_quote($echeance),
					'id_abonnements_offre = '.intval($notification['id_abonnements_offre']),
					'email is not null'
				)
			)){
				// Pour chacun on programme un envoi de mail
				foreach ($a_notifier as $abonne){
					$id_job = job_queue_add(	
						'abonnements_notifier_echeance',
						"Notifier ${abonne['nom']} ${notification['duree']} ${notification['periode']} avant la fin de son abonnement ${abonne['id_abonnement']}",
						array($abonne['id_abonnement'], $abonne['nom'], $abonne['email'], $notification['duree'], $notification['periode']),
						'inc/abonnements',
						true
					);
					job_queue_link($id_job, array('objet'=>'abonnement', 'id_objet'=>$abonne['id_abonnement']));
				}
			}
		}
	}
	
	return 1;
}

?>
