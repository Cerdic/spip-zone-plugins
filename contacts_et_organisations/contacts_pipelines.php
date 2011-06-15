<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */
 

/**
 * Affichage du formulaire de choix Contact/Organisation
 * dans la colonne de vue d'un auteur
 * et 
 * Affichage du formulaire de recherche et de sélection d'Organisations
 * dans la colonne de vue d'une rubrique
**/
function contacts_affiche_gauche($flux){

	if ($flux['args']['exec'] == 'auteur_infos'){
		$flux['data'] .= recuperer_fond('prive/boite/selecteur_contacts_organisations', array(
			'id_auteur'=>$flux['args']['id_auteur']
		), array('ajax'=>true));
	}

	if ($flux['args']['exec'] == 'naviguer' && $flux['args']['id_rubrique']){
		$flux['data'] .= recuperer_fond('prive/boite/selecteur_organisations_de_rubrique', array(
			'id_rubrique'=>$flux['args']['id_rubrique']
		));
	}	

	return $flux;
}


/**
 *
 * Insertion dans la vue des auteurs
 * des informations relatives aux contacts et organisations
 * et
 * Insertion dans la vue des rubriques
 * des informations relatives aux organisations
 */
function contacts_affiche_milieu($flux){
	if ($flux['args']['exec'] == 'auteur_infos') {
		$data  = recuperer_fond('prive/contenu/contact',
			array('id_auteur' => $flux['args']['id_auteur'], 'cadre'=>'oui'));
		$data .= recuperer_fond('prive/contenu/organisation',
			array('id_auteur' => $flux['args']['id_auteur'], 'cadre'=>'oui'));
		$flux['data'] = $data . $flux['data'];
		}
		
	if ($flux['args']['exec'] == 'naviguer' && $flux['args']['id_rubrique']){
		$flux['data'] .= recuperer_fond('prive/liste/organisations_liees_rubrique', array(
			'id_rubrique' => $flux['args']['id_rubrique'],
			'titre' => _T('contacts:info_organisations_appartenance')
		), array('ajax'=>true));
	}
	return $flux;
}


/**
 * Prendre en compte les tables dans la recherche d'éléments. 
 *
 * @param 
 * @return 
**/
function contacts_rechercher_liste_des_champs($tables){
	
	// ajouter la recherche sur contact
	$tables['contact']['id_contact'] = 12;
	$tables['contact']['nom'] = 4;
	$tables['contact']['prenom'] = 2;
	
	// ajouter la recherche sur organisations
	$tables['organisation']['id_auteur'] = 12;
	$tables['organisation']['nom'] = 4;

	return $tables;
}


/**
 * Autoriser les champs extras sur les objets
 * Contacts et Organisations
**/
function contacts_objets_extensibles($objets){
		return array_merge($objets, array(
			'contact' => _T('contacts:contacts'),
			'organisation' => _T('contacts:organisations'),
		));
}

/**
 * Ajoute une feuille de style pour la v-card
 * Peut être surchargé ensuite
**/
function contacts_insert_head($flux){

	$flux .= '<!-- insertion de la css contacts--><link rel="stylesheet" type="text/css" href="'.find_in_path('contacts.css').'" media="all" />';

	return $flux;
}


?>
