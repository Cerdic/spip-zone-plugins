<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function abonnements_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('abonnement'=>'*'),'*');
	return $flux;
}

/*
 * Des modifs supplémentaires après édition
 */
function abonnements_post_edition($flux){
	// Si on modifie un abonnement
	if ($flux['args']['table'] == 'spip_abonnements') {
		$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = '.$flux['args']['id_objet']);
		$offre = sql_fetsel('*', 'spip_abonnements_offres', 'id_abonnements_offre = '.$abonnement['id_abonnements_offre']);
		
		$modifs = array();
		
		// Si l'échéance est VIDE, et que pourtant l'offre parente A BIEN une durée
		// alors c'est qu'il faut initialiser l'échéance !
		if ($abonnement['date_fin'] == '0000-00-00 00:00:00' and ($duree = $offre['duree']) > 0){
			// De combien doit-on augmenter la date
			switch ($offre['periode']){
				case 'heures':
					$ajout = " + ${duree} hours";
					break;
				case 'jours':
					$ajout = " + ${duree} days";
					break;
				case 'mois':
					$ajout = " + ${duree} months";
					break;
				default:
					$ajout ='';
					break;
			}
			
			// Calcul de la date de fin
			$modifs['date_fin'] = date('Y-m-d H:i:s', strtotime($abonnement['date_debut'].$ajout));
		}
		
		// S'il le statut est "prepa" c'est une création et on doit changer ça
		// car pour l'instant SPIP ne permet pas de déclarer le statut par défaut !
		if ($abonnement['statut'] == 'prepa'){
			$modifs['statut'] = 'actif';
		}
		
		// S'il y a des modifs à faire on appelle l'API de modif
		if (!empty($modifs)){
			include_spip('action/editer_objet');
			objet_modifier('abonnement', $flux['args']['id_objet'], $modifs);
		}
		
		// Si dans les modifications demandées au départ, il y a la date de fin, on reprogramme la désactivation
		if (isset($flux['data']['date_fin'])) {
			include_spip('inc/abonnements');
			abonnements_programmer_desactivation($flux['args']['id_objet'], $flux['data']['date_fin']);
		}
	}
	
	return $flux;
}

/*
 * Ajout d'une tache CRON pour vérifier toutes les heures si les abonnements actifs ont une tâche de désactivation
 */
function abonnements_taches_generales_cron($taches){
	$taches['abonnements_verifier_desactivation'] = 60 * 60;
	return $taches;
}

?>
