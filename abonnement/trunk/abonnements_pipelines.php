<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Optimiser la base de donnees des abonnements
 *
 * @param int $n
 * @return int
 */
function abonnements_optimiser_base_disparus($flux){

	//Offres d'abonnement à la poubelle
	sql_delete("spip_abonnements_offres", "statut='poubelle' AND maj < ".$flux['args']['date']);
	
	//Supprimer les abonnements lies à une offre d'abonnement inexistante
	$res = sql_select("DISTINCT abonnements.id_abonnements_offre","spip_abonnements AS abonnements
						LEFT JOIN spip_abonnements_offres AS offres
						ON abonnements.id_abonnements_offre=offres.id_abonnements_offre","offres.id_abonnements_offre IS NULL");
	while ($row = sql_fetch($res))
		sql_delete("spip_abonnements", "id_abonnements_offre=".$row['id_abonnements_offre']);

	//Abonnements à la poubelle
	sql_delete("spip_abonnements", "statut='poubelle' AND maj < ".$flux['args']['date']);
	
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
	$taches['abonnements_verifier_desactivation'] = 60 * 60; // toutes les heures
	$taches['abonnements_verifier_notifications'] = 24 * 3600; // une fois par jour
	return $taches;
}

/*
 * Ajouter la config des notifications
 */
function abonnements_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'abonnements_offre'){
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/navigation/inc-abonnements_notifications',
			array('id_abonnements_offre' => $flux['args']['id_objet'])
		);
	}
	
	return $flux;
}

/*
 * Ajouter la boite des abonnements sur la fiche auteur
 */
function abonnements_affiche_milieu($flux){

	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'auteur'
	  AND $e['edition'] == false) {
		
		$id_auteur = $flux['args']['id_auteur'];

		$ins = recuperer_fond('prive/squelettes/inclure/abonnements_auteur',array('id_auteur'=>$id_auteur));
		if (($p = strpos($flux['data'],"<!--affiche_milieu-->")) !== false)
			$flux['data'] = substr_replace($flux['data'],$ins,$p,0);
		else
			$flux['data'] .= $ins;
		
	}
	
	return $flux;
}
 
?>
