<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Afficher un formulaire de liaison de zones sur les offres d'abonnement
 */
function abozones_afficher_complement_objet($flux){
	// Si on est en train de visualiser une offre d'abonnement
	if ($flux['args']['type'] == 'abonnements_offre'){
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/abonnements_offre-zones',
			array('id_abonnements_offre' => $flux['args']['id'])
		);
	}
	
	return $flux;
}

/*
 * Ajouter ou retirer à un utilisateur des zones liées à une offre suivant divers événements
 */
function abozones_post_edition($flux){
	// Lorsqu'un abonnement est créé ou change de statut... et que l'offre est liée à des zones !
	if (
		$flux['args']['table'] == 'spip_abonnements'
		and (($flux['args']['action'] == 'modifier') or ($flux['args']['action'] == 'instituer'))
		and $id_abonnement = intval($flux['args']['id_objet'])
		and $abonnement = sql_fetsel('id_abonnements_offre,id_auteur', 'spip_abonnements', 'id_abonnement = '.$id_abonnement)
		and $id_auteur = $abonnement['id_auteur']
		and $id_abonnements_offre = $abonnement['id_abonnements_offre']
		and include_spip('action/editer_liens')
		and $liens = objet_trouver_liens(array('zones'=>'*'), array('abonnements_offre'=>$id_abonnements_offre))
	) {
		include_spip('inc/autoriser');
		include_spip('action/editer_zone');
		
		// On ne récupère que les ids
		$zones = array();
		foreach ($liens as $lien) {
			$zones[] = $lien['id_zone'];
		}
		
		// Si c'est une activation on ajoute les zones trouvées à l'utilisateur de l'abonnement SANS autorisation
		if (
			(($flux['data']['statut'] == 'actif') or !isset($flux['data']['statut']))
			and !isset($flux['data']['statut_ancien'])						
		) {
			autoriser_exception('affecterzones', 'auteur', $id_auteur);
			zone_lier($zones, 'auteur', $id_auteur);
			autoriser_exception('affecterzones', 'auteur', $id_auteur, false);
		}
		// Si c'est une désactivation (ancien statut actif, et nouveau différent)
		// on supprime les zones
		elseif (
			$flux['args']['statut_ancien'] == 'actif'
			and isset($flux['data']['statut'])
			and $flux['data']['statut'] != 'actif'
		) {
			autoriser_exception('retirerzones', 'auteur', $id_auteur);
			zone_lier($zones, 'auteur', $id_auteur, 'del');
			autoriser_exception('retirerzones', 'auteur', $id_auteur, false);
		}
	}
	
	return $flux;
}

/*
 * Ajouter ou retirer à un utilisateur une zone qui vient d'être liée ou déliée à une offre
 */
function abozones_post_edition_lien($flux){
	// Lorsqu'on vient de modifier un lien de zone pour une offre, et que celle-ci a des abonnements actifs
	if (
		$flux['args']['objet_source'] == 'zone'
		and $id_zone = intval($flux['args']['id_objet_source'])
		and $flux['args']['objet'] == 'abonnements_offre'
		and $id_abonnements_offre = intval($flux['args']['id_objet'])
		and $auteurs_actifs = sql_allfetsel(
			'u.id_auteur',
			'spip_auteurs as u join spip_abonnements as a on a.id_auteur=u.id_auteur',
			array('a.id_abonnements_offre = '.$id_abonnements_offre, 'a.statut = "actif"')
		)
		and is_array($auteurs_actifs)
	) {
		include_spip('inc/autoriser');
		include_spip('action/editer_zone');
		
		// Pour chacun des auteurs ayant un abonnement actif de l'offre
		$auteurs_actifs = array_map('reset', $auteurs_actifs);
		foreach ($auteurs_actifs as $id_auteur){
			// Si c'était un ajout de zone, on ajoute la zone aux auteurs
			if ($flux['args']['action'] == 'insert') {
				autoriser_exception('affecterzones', 'auteur', $id_auteur);
				zone_lier($id_zone, 'auteur', $id_auteur);
				autoriser_exception('affecterzones', 'auteur', $id_auteur, false);
			}
			// Si c'était une suppression de zone, on supprime la zone aux auteurs
			elseif ($flux['args']['action'] == 'delete') {
				autoriser_exception('retirerzones', 'auteur', $id_auteur);
				zone_lier($id_zone, 'auteur', $id_auteur, 'del');
				autoriser_exception('retirerzones', 'auteur', $id_auteur, false);
			}
		}
	}	
}

?>
