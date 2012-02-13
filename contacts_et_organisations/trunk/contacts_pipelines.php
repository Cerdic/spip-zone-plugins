<?php

/**
 * Plugin Contacts & Organisations pour Spip 3.0
 * Licence GPL (c) 2009 - 2012 - Ateliers CYM
 */
 

/**
 * Ajouter un fil d'ariane
 * sur les auteurs
 * définis comme contacts ou organisation
 */
function contacts_affiche_hierarchie($flux)
{
	if ($flux['args']['objet'] == 'auteur') {
		$id = intval($flux['args']['id_objet']);
		// cherchons un contact
		if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id)) {
			$flux['data'] = recuperer_fond('prive/squelettes/hierarchie/contact', array('id_contact'=>$id_contact)) . '<br />' . $flux['data'];
		// sinon une organisation
		} elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id)) {
			$flux['data'] = recuperer_fond('prive/squelettes/hierarchie/organisation', array('id_organisation'=>$id_organisation)) . '<br />' . $flux['data'];
		}
	}

	return $flux;
}


/**
 *
 * Insertion dans la vue des auteurs
 * des informations relatives aux contacts et organisations
 *
 */
function contacts_afficher_contenu_objet($flux)
{
	if ($flux['args']['type'] == 'auteur') {
		$id = intval($flux['args']['id_objet']);

		// informations sur le contact et ses liens
		if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id))
		{
			$infos = recuperer_fond('prive/squelettes/contenu/contact_sur_auteur', array('id_contact' => $id_contact));
			$flux['data'] .= $infos;
		}
		// informations sur l'organisation et ses liens
		elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id))
		{
			$infos = recuperer_fond('prive/squelettes/contenu/organisation_sur_auteur', array('id_organisation' => $id_organisation));
			$flux['data'] .= $infos;
		}

	}

	return $flux;
}



/**
 * Affichage du formulaire de choix Contact/Organisation
 * dans la colonne de vue d'un auteur
 * et 
 * Affichage du formulaire de recherche et de sélection d'Organisations
 * dans la colonne de vue d'une rubrique
**/
function contacts_affiche_gauche($flux){

	if ($flux['args']['exec'] == 'auteur'){

		$flux['data'] .= recuperer_fond(
			'prive/old/boite/selecteur_contacts_organisations',
			array(
				'id_auteur' => $flux['args']['id_auteur'] 
			)
		); 
	
	}

	if ($flux['args']['exec'] == 'naviguer' && $flux['args']['id_rubrique']){
		$flux['data'] .= recuperer_fond('prive/old/boite/selecteur_organisations_de_rubrique', array(
			'id_rubrique'=>$flux['args']['id_rubrique']
		));
	}

	return $flux;
}


/**
 *
 * Insertion dans la vue des rubriques
 * des informations relatives aux organisations
 */
function contacts_affiche_milieu($flux){
	if ($flux['args']['exec'] == 'rubriques' && $flux['args']['id_rubrique'])
	{
		$flux['data'] .= recuperer_fond('prive/old/liste/organisations_liees_rubrique', array(
			'id_rubrique' => $flux['args']['id_rubrique'],
			'titre' => _T('contacts:info_organisations_appartenance')
		), array('ajax'=>true));
	} // fin page rubrique

	return $flux;
}




/**
 * Ajoute une feuille de style pour la v-card
 * Peut être surchargé ensuite
**/
function contacts_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('contacts.css').'" media="all" />';
	return $flux;
}

?>
