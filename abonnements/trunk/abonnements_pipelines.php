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
		include_spip('inc/abonnements');
		$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = '.intval($flux['args']['id_objet']));
		$offre = sql_fetsel('*', 'spip_abonnements_offres', 'id_abonnements_offre = '.intval($abonnement['id_abonnements_offre']));
		$jourdhui = date('Y-m-d H:i:s');
		
		// Si la date de fin a été modifiée et qu'elle est dans le future
		// on reprogramme la désactivation
		if (isset($flux['data']['date_fin']) and $flux['data']['date_fin'] > $jourdhui) {
			abonnements_programmer_desactivation($flux['args']['id_objet'], $flux['data']['date_fin']);
		}
		
		// Si on a mis l'abonnement inactif ou à la poubelle, on doit enlever les tâches liées
		if (
			isset($flux['data']['statut'])
			and in_array($flux['data']['statut'], array('inactif', 'poubelle'))
		) {
			include_spip('action/editer_liens');
			$liens = objet_trouver_liens(array('job' => '*'), array('abonnement' => $abonnement['id_abonnement']));
			if ($liens and is_array($liens)){
				// Et on les supprime toutes !
				foreach ($liens as $lien){
					job_queue_remove($lien['id_job']);
				}
			}
		}
		
		$modifs = array();
		
		// Si l'échéance est VIDE, et que pourtant l'offre parente A BIEN une durée
		// alors c'est qu'il faut initialiser les dates !
		if ($abonnement['date_echeance'] == '0000-00-00 00:00:00' and ($duree = $offre['duree']) > 0) {
			$modifs = abonnements_initialisation_dates($abonnement, $offre);
		}
		
		// Si les dates doivent être changées, on change le tableau de l'abonnement pour le test de statut qui suivra
		if (isset($modifs['date_debut'])) {
			$abonnement['date_debut'] = $modifs['date_debut'];
		}
		if (isset($modifs['date_fin'])) {
			$abonnement['date_fin'] = $modifs['date_fin'];
		}
		
		// Seulement si personne n'a modifié le statut manuellement, alors on check les dates pour statufier
		if (!$flux['data']['statut']) {
			// Si aujourd'hui est entre date_debut et date_echeance, on active
			if (
				$abonnement['statut'] == 'inactif'
				and $jourdhui >= $abonnement['date_debut']
				and $jourdhui <= $abonnement['date_echeance']
			) {
				$modifs['statut'] = 'actif';
			}
			// Si aujourd'hui est en dehors des dates début et FIN, on désactive
			// on ne teste pas date_echeance car ce sera à un génie de désactiver si trop dépassée
			elseif (
				$abonnement['statut'] == 'actif'
				and ($jourdhui < $abonnement['date_debut'] or $jourdhui >= $abonnement['date_fin'])
			) {
				$modifs['statut'] = 'inactif';
			}
		}
		
		// S'il y a des modifs à faire on appelle l'API de modif
		if (!empty($modifs)){
			include_spip('action/editer_objet');
			objet_modifier('abonnement', $flux['args']['id_objet'], $modifs);
		}
	}
	// Détection magique du plugin Commandes et d'une commande d'offre d'abonnement
	elseif (
		// Si on institue une commande
		$flux['args']['table'] == 'spip_commandes'
		and $id_commande = intval($flux['args']['id_objet'])
		and $flux['args']['action'] == 'instituer'
		// Et qu'on passe en statut "paye" depuis autre chose
		and $flux['data']['statut'] == 'paye'
		and $flux['args']['statut_ancien'] != 'paye'
		// Et que la commande existe bien
		and $commande = sql_fetsel('*', 'spip_commandes', 'id_commande = '.$id_commande)
		// Et que cette commande a un utilisateur correct
		and ($id_auteur = $commande['id_auteur']) > 0
		// Et qu'on a des détails dans cette commande
		and $details = sql_allfetsel('*', 'spip_commandes_details', 'id_commande = '.$id_commande)
		and is_array($details)
	) {
		// On cherche si on a des offres d'abonnements dans les détails de la commande
		foreach ($details as $detail) {
			// Si on trouve une offre d'abonnement
			if ($detail['objet'] == 'abonnements_offre' and ($id_abonnements_offre = $detail['id_objet']) > 0) {
				// Si la commande est renouvelable et que c'est le PREMIER paiement (activation)
				// on force toujours la création d'un nouvel abonnement
				$forcer_creation = false;
				if (
					in_array($commande['echeances_type'], array('mois', 'annee'))
					and include_spip('inc/commandes_echeances')
					and commandes_nb_echeances_payees($id_commande) <= 1
				) {
					$forcer_creation = true;
				}
				
				// On crée ou renouvelle
				include_spip('inc/abonnements');
				$retour = abonnements_creer_ou_renouveler($id_auteur, $id_abonnements_offre, $forcer_creation);
				
				// Si on a un retour correct avec un abonnement
				if (
					is_array($retour)
					and $id_abonnement = intval(reset($retour))
					and $id_abonnement > 0
				) {
					// On lie cet abonnement avec la commande qui l'a généré
					include_spip('action/editer_liens');
					objet_associer(
						array('commande' => $id_commande),
						array('abonnement' => $id_abonnement)
					);
				}
			}
		}
	}
	
	return $flux;
}

/*
 * Ajout de tâches nécessaires aux abonnements
 * 
 * - Une tâche pour vérifier toutes les heures si on a pas trop dépassé des échéances
 * - Une tâche pour vérifier toutes les heures si les abonnements actifs ont une tâche de désactivation
 * - Une tâche pour programmer les emails de notification à envoyer
 * 
 * @pipeline taches_generales_cron
 * @param array $taches Liste des génies et leur périodicité
 * @return array Liste des tâches possiblement modifiées
 */
function abonnements_taches_generales_cron($taches){
	$taches['abonnements_verifier_echeances'] = 60 * 60; // toutes les heures
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
			array('id_abonnements_offre' => $flux['args']['id_abonnements_offre'])
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
