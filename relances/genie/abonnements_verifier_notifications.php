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
			
			//if (_DEBUG_RELANCE) spip_log("Aujourd'hui l'echeance pour la relance N°".$relance['id_relance']." doit être $echeance","relance");			
			
			// Pour chaque relance on cherche tous les abonnemment contractés ayant cet échéance
			if ($a_notifier = sql_allfetsel(
				'*','spip_contacts_abonnements',
				array(
					'DATE_FORMAT(validite, "%Y-%m-%d") = '.sql_quote($echeance),
					'objet = "abonnement"'
				)
			)){
							
				// Pour chacun on programme un envoi de mail
				foreach ($a_notifier as $notif){
					
					$id_relance=$relance['id_relance'];
					$id_abonnement = $notif['id_objet'];
					$id_auteur=$notif['id_auteur'];
					$id_contacts_abonnement=$notif['id_contacts_abonnement'];
					

					//si un même auteur a plusieurs fois la même offre, on doit éliminer la plus ancienne						
					$dernier_id_contacts_abonnement = sql_getfetsel(
						'id_contacts_abonnement','spip_contacts_abonnements',
						array(
							'id_auteur = '.sql_quote($id_auteur),
							'objet = "abonnement"',
							'id_objet = '.sql_quote($id_abonnement)
						),'',array('validite'." DESC "),"0,1"
					);
										
					//ne relancer que le dernier abonnement (par exemple si il a déjà été repris)
					if ($id_contacts_abonnement==$dernier_id_contacts_abonnement){
					
						//on verifie que la relance n'a pas déjà été effectuée ce jour
						$today = date('Y-m-d');
						$relance_deja=sql_getfetsel("id_relances_archive","spip_relances_archives",
							array(
								"id_relance=$id_relance",
								"id_abonnement=$id_abonnement",
								"id_auteur=$id_auteur",
								'DATE_FORMAT(date, "%Y-%m-%d")='.sql_quote($today)
							)
						);
											
						if(!isset($relance_deja)){
							if (_DEBUG_RELANCE) spip_log("relancer $nom N°id_auteur ".$id_auteur." pour l'abonnement ".$id_abonnement." id_contacts_abonnement=".$id_contacts_abonnement,"relance");
							
							//on va chercher email et nom de l'auteur
							$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
							$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
		
							if (function_exists('job_queue_add'))
							job_queue_add(	
								'abonnements_notifier_echeance',
								"Notifier auteur ".$id_auteur." de l'échéance de son abonnement",
								array($id_abonnement, $id_relance, $id_auteur, $relance['titre'], $nom, $email, $relance['duree'], $relance['periode'], 'html'),
								'inc/abonnements',
								true
							);
						}
					}
				}
			}
		}
	}
	
	return 1;
}

?>
