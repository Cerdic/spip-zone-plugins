<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 * (selecteur des auteurs a abonner)
 */

function abonnement_inserer_js_recherche_objet(){
	return <<<EOS

		function rechercher_objet(id_selecteur, page_selection) {
			// chercher l'input de saisie
			var me = jQuery(id_selecteur+' input[name=nom_objet]');
			me.autocomplete(page_selection,
					{
						delay: 200,
						autofill: false,
						minChars: 1,
						multiple:false,
						multipleSeparator:";",
						formatItem: function(data, i, n, value) {
							return data[0];
						},
						formatResult: function(data, i, n, value) {
							return data[1];
						}
					}
				);
				me.result(function(event, data, formatted) {
					if (data[2] > 0) {
						jQuery(id_selecteur + ' #pid_objet').val(data[2]);
						jQuery(id_selecteur + ' input[type="submit"]').focus();
						jQuery(me)
						.end();
					}
					else{
						return data[1];
					}
				});
			};
EOS;
}

function abonnement_inserer_javascript($flux){
	include_spip('selecteurgenerique_fonctions');
	/*$flux .= selecteurgenerique_verifier_js($flux);*/

	$js = abonnement_inserer_js_recherche_objet();
	$js = "<script type='text/javascript'><!--\n$js\n// --></script>\n";

	return $flux.$js;
}

// espace public
// si sql spip_commandes_details => gerer l'abonnement aux objets avec statut_commande repris
function abonnement_post_insertion($flux){
	
	//pour les details (voir panier2commande) a la creation des details de la commande
	if (
		$flux['args']['table'] == 'spip_commandes_details'
		and ($id_commande_detail = intval($flux['args']['id_objet'])) > 0
		and ($id_commande = intval($flux['data']['id_commande'])) > 0
	){
			
		//on recupere le statut de la commande
		$commande=sql_fetsel(
			'statut,id_auteur',
			'spip_commandes',
			'id_commande = '.$id_commande
		);
		
		// si dans le detail de la commande il y a abonnement, article ou rubrique en objet
		$objet=$flux['data']['objet'];
		$objets=array('article','rubrique','abonnement');
		
		if(in_array($objet, $objets)){
			$id_objet=$flux['data']['id_objet'];
			$prix=$flux['data']['prix_unitaire_ht'];
			
		if (_DEBUG_ABONNEMENT) spip_log("flux details id_auteur=".$commande['id_auteur']." $statut pour objet = $objet id_commande_detail= $id_commande_detail",'abonnement');

					$arg=array(
						'id_auteur'=>$commande['id_auteur'],
						'objet'=>$objet,
						'id_objet' => $id_objet,
						'id_commandes_detail'=>$id_commande_detail,
						'statut_abonnement'=>$commande['statut'],
						);
					include_spip('action/editer_contacts_abonnement');
					insert_contacts_abonnement($arg);
		}
	}
	
	//pour les details a la modif de la commande
	if (
		$flux['args']['table'] == 'spip_commandes'
		and ($id_commande = intval($flux['args']['id_objet'])) > 0
		and ($id_auteur = intval($flux['data']['id_auteur'])) > 0
	){
			
		//statut de la commande
		$statut=$flux['data']['statut'];
		
		if (_DEBUG_ABONNEMENT) spip_log("modif id_commande=$id_commande statut=$statut pour auteur=$id_auteur",'abonnement');
		
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
					
					$id_commandes_detail=$abo['id_commandes_detail'];
					$id_objet=$abo['id_objet'];
					
					//recupere id_contacts_abonnement si il existe
					$contact_abo = sql_fetsel('id_contacts_abonnement,statut_abonnement', 'spip_contacts_abonnements', 'id_commandes_detail='.$id_commandes_detail);
					
					$id_contacts_abonnement=$contact_abo['id_contacts_abonnement'];
					if($id_contacts_abonnement){
						if (_DEBUG_ABONNEMENT) spip_log("pour id_contacts_abonnement = $id_contacts_abonnement on fait ".'id_commandes_detail='.$id_commandes_detail,'abonnement');
	
						
						//on institue le contacts_abonnement
						$action = charger_fonction('instituer_contacts_abonnement', 'action');
						$action($id_contacts_abonnement."-".$statut);
					}
					else {
						$champs['id_auteur']=$id_auteur;
						$champs['statut_abonnement']=$statut;
						$champs['objet']=$objet;
						$champs['id_objet']=$id_objet;
						$champs['id_commandes_detail']=$id_commandes_detail;
						include_spip('action/editer_contacts_abonnement');
						$id_contacts_abonnement = insert_contacts_abonnement($champs);
					}
				}
		}

	}

return $flux;
	
}             


//espace prive
//affiche les abonnements auxquels l'auteur est abonne (sur auteur_infos)
function abonnement_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos' && $id_auteur=$flux['args']['id_auteur']) {
		include_spip('inc/presentation');
		$flux['data'] .= recuperer_fond('prive/boite/contacts_abonnements', array('page_envoi'=>'auteur_infos','id_auteur'=>$id_auteur), array('ajax'=>true));
	}

	if ($exec = $flux['args']['exec']){
		switch ($exec){
			case 'articles':
				$source = 'article';
				$id_source = $flux['args']['id_article'];
				break;
			case 'abonnement_edit':
				$source = 'abonnement';
				$id_source = $flux['args']['id_abonnement'];
				break;
			case 'naviguer':
				$source = 'rubrique';
				$id_source = $flux['args']['id_rubrique'];
				break;
			/*
			//abonnes a un mot-clef
			case 'mots_edit':
				$source = 'mot';
				$id_source = $flux['args']['id_mot'];
				break;
			*/
			default:
				$source = $id_source = '';
				break;
		}
		if ($source && intval($id_source)) {
	
//a partir de source et id_source on retrouve les abonnes en cascade
//on les affiche avec l'icone de leur abonnement (article-rubrique-abonnement)?

		$contexte= array(
			'objet' => 'auteurs',
			'titre_bouton'=>_T('abo:titre_les_abonnes'),
			'source'=>$source,
			'id_source'=> $id_source,
			'id_table_source'=>'spip_contacts_abonnements',
			);
		
		$flux['data'] .= recuperer_fond("prive/liste/lister-contacts_abonnements", $contexte);	
		
		}
	}
	
	return $flux;
}

// Supprimer tous les contacts_abonnements en cours et trop vieux
function abonnement_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la durée de vie d'un contacts_abonnements encours (par défaut 1h)
	$depuis = date('Y-m-d H:i:s', time() - 3600);
	
	// On récupère les contacts_abonnements trop vieux
	$contacts_abonnements = sql_allfetsel(
		'id_contacts_abonnement',
		'spip_contacts_abonnements',
		'statut_abonnement = '.sql_quote('encours').' and date<'.sql_quote($depuis)
	);
	if (is_array($contacts_abonnements))
		$contacts_abonnements = array_map('reset', $contacts_abonnements);
	
	// S'il y a bien des contacts_abonnements à supprimer
	if ($contacts_abonnements){
		// Le in
		$in = sql_in('id_contacts_abonnement', $contacts_abonnements);
		
		// Les contacts_abonnements
		$nombre = intval(sql_delete(
			'spip_contacts_abonnements',
			$in
		));
	}
	
	$flux['data'] += $nombre;
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

		
//reprendre la meme fonction que post_insertion pour traiter les details de la commande
		if (
			$flux['args']['table'] == 'spip_commandes'
		){
		if (_DEBUG_ABONNEMENT) spip_log("abonnement_post_edition args ".join(",\n", $flux['args'])." data= ".join(",\n", $flux['data']),'abonnement');
		$flux = abonnement_post_insertion($flux);
		}

	return $flux;
}



//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement'] = 60*60*24 ;
	return $taches_generales;
}



?>
