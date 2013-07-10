<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Vérifie si aujourd'hui on a des relances à envoyer à certains abonnés
 */
function genie_abonnements_verifier_notifications_dist($time){
	include_spip('base/abstract_sql');
	$jourdhui = date('Y-m-d H:I:s');
	
	// On va chercher toutes les relances
	if (
		$relances = sql_allfetsel('*', 'spip_relances')
		and is_array($relances)
	){
		// Pour chaque relance on va chercher les abonnés dont c'est le moment
		foreach ($relances as $relance){
			
			//avant ou après la date de fin de l'abonnement?
			if($relance['quand']=="apres") $operateur=" - "; 
			else $operateur=" + ";
			
			// De combien doit-on modifier la date
			switch ($relance['periode']){
				case 'jours':
					$ajout = " $operateur ${relance['duree']} days";
					break;
				case 'mois':
					$ajout = " $operateur ${relance['duree']} months";
					break;
				default:
					$ajout ='';
					break;
			} 
			
			// S'il y a de quoi ajouter (ou soustraire) alors on va calculer l'échéance à chercher
			if ($ajout){
				$echeance = date('Y-m-d', strtotime($jourdhui.$ajout));
			}
			
			spip_log("echeance de la relance ".$relance['id_relance']." est pour $echeance","relance");			
			
			// Pour cette relance on cherche donc tous les abonnés ayant cet échéance avec la même offre
			if ($a_notifier = sql_allfetsel(
				'id_contacts_abonnement, a.id_auteur, objet, id_objet, nom, email',
				'spip_contacts_abonnements as a left join spip_auteurs as u on a.id_auteur=u.id_auteur',
				array(
					'DATE_FORMAT(validite, "%Y-%m-%d") = '.sql_quote($echeance),
					'objet = "abonnement"',
					//'id_abonnement = '.intval($relance['id_abonnement']),
					'email is not null'
				)
			)){
			spip_log("On a trouvé au moins un abonnement se terminant le $echeance","relance");
			
				// Pour chacun on programme un envoi de mail
				foreach ($a_notifier as $abonne){
					$id_job = job_queue_add(	
						'abonnements_notifier_echeance',
						"Notifier ${abonne['nom']} ${relance['duree']} ${relance['periode']} avant la fin de son abonnement ${abonne['id_abonnement']}",
						array($abonne['id_objet'], $relance['id_relance'], $abonne['id_auteur'], $relance['titre'], $abonne['nom'], $abonne['email'], $relance['duree'], $relance['periode'], 'html'),
						'inc/abonnements',
						true
					);
					job_queue_link($id_job, array('objet'=>'contacts_abonnement', 'id_objet'=>$abonne['id_contacts_abonnement']));
					
				}
			}
		}
	}
	
	return 1;
}

?>
