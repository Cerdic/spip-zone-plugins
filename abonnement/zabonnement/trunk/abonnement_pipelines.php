<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('action/editer_contactabonnement');

// espace public
// si sql commande inserer l'abonnement aux objets avec statut_commande repris
function abonnement_post_insertion($flux){
		
	if (
		$flux['args']['table'] == 'spip_commandes'
		and ($id_commande = intval($flux['args']['id_objet'])) > 0
		and ($id_auteur = intval($flux['args']['id_auteur'])) > 0
	){
			
		//statut de la commande
		$statut=$flux['data']['statut'];
		
		if (_DEBUG_ABONNEMENT) spip_log("a_p_i id_commande=$id_commande $statut_commande pour auteur $id_auteur",'abonnement');
		
		// si dans les details de la commande il y a abonnement, article ou rubrique en objet
		$objets=array('article','rubrique','abonnement');
		foreach($objets as $objet){
			if (_DEBUG_ABONNEMENT) spip_log("a_p_i objet= $objet",'abonnement');		

			$commande_abos = sql_allfetsel(
				'*',
				'spip_commandes_details',
				'id_commande = '.$id_commande." AND objet='$objet'"
			);
			
				// Pour chaque commande contenant article ou rubrique en objet
				foreach($commande_abos as $abo){

					$arg=array(
						'id_auteur'=>$id_auteur,
						'statut'=>$statut,
						'objet'=>$abo['objet'],
						'table'=>"spip_".$objet."s",
						'ids' => array($abo['id_objet']), //tjs envoyer un array
						'prix'=>$abo['prix_unitaire_ht'],
						'duree'=>3,
						'periode'=>'jour',
						'id_commandes_detail'=>$abo['id_commandes_detail'],
					);

					editer_contactabonnement($arg);
				}
		}

	}
	
return $flux;
	
}             


//espace prive
//affiche les abonnements auxquels l'auteur est abonne (sur auteur_infos)
function abonnement_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
		$legender_auteur_supp = recuperer_fond('prive/abonnement_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= $legender_auteur_supp;
	}
	//todo tester le flux pour savoir si abonnement ok
	/*
	//affiche les auteurs dont l'abonnement comprend cet article (sur articles)	
	if($flux['args']['exec'] == 'articles') {
		$affiche_abonnes = recuperer_fond('prive/liste/abonnes_article',
			array('id_objet'=>$flux['args']['id_article'],	
			'objet'=>'article'
			));
		$flux['data'] .= $affiche_abonnes;
	}
	
	
	//affiche les auteurs dont l'abonnement comprend cette rubrique (sur rubriques)	
	if($flux['args']['exec'] == 'naviguer') {
		$affiche_abonnes = recuperer_fond('prive/liste/abonnes_rubrique',
			array('id_objet'=>$flux['args']['id_rubrique'],	
			'objet'=>'rubrique'
			));
		$flux['data'] .= $affiche_abonnes;
	}
	*/
	
	return $flux;
}


//affiche liste des abonnements pour s'abonner dans le formulaire d'un auteur
function abonnement_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		$abonnement = recuperer_fond('prive/abonnement_fiche_modif',array('id_auteur'=>$flux['args']['id']));
		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$abonnement, $flux['data']);
	}
	return $flux;
}


/**
 *
 * Insertion dans le pipeline post_edition
 * ajouter les champs abonnement soumis lors de la soumission du formulaire CVT editer_auteur
 *
 * @return
 * @param object $flux
 */
  /*
$flux['args']['action'] = modifier
 il faudrait savoir si on est dans prive ou en public
 todo car determine si paiement possible...
 ne sert actuellement que dans le backoffice en 'cadeau' (statut paye sans lien a une commande = offert)
 */
function abonnement_post_edition($flux){
		
	// lors de l'edition d'un auteur
	if ($flux['args']['table']=='spip_auteurs') {
		
		//valable 3 jours, todo in config
		$statut ='offert';
		$duree = 3; 
		$id_auteur=$flux['args']['id_objet'];
		
		$objets=array('article','rubrique','abonnement');
		
		foreach($objets as $objet){
			$ids=_request($objet.'s');
			if ($ids && is_array($ids)) {				
				$args=array(
				'id_auteur'=> $id_auteur,
				'objet'=>$objet,
				'table'=>"spip_".$objet."s",
				'ids' => $ids,
				'duree'=>$duree,
				'statut'=>$statut
				);
				if (_DEBUG_ABONNEMENT) spip_log("APE args ".$args['table'] ." ids0=".$args['ids'][0],'abonnement');
				editer_contactabonnement($args);
			}	
		}

	}

	return $flux;
}



//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement'] = 60*60*24 ;
	return $taches_generales;
}

?>
