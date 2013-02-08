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
	if ($flux['args']['objet'] == 'auteur'
        and $id = $flux["args"]["id_auteur"]) {
		if (lire_config('contacts_et_organisations/associer_aux_auteurs')) {
			$id = intval($flux['args']['id_objet']);
			// cherchons un contact
			if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id)) {
				$flux['data'] = recuperer_fond('prive/squelettes/hierarchie/contact', array('id_contact'=>$id_contact)) . '<br />' . $flux['data'];
			// sinon une organisation
			} elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id)) {
				$flux['data'] = recuperer_fond('prive/squelettes/hierarchie/organisation', array('id_organisation'=>$id_organisation)) . '<br />' . $flux['data'];
			}
		}
	}

	return $flux;
}


/**
 *
 * Insertion dans la vue des auteurs
 * des informations relatives aux contacts et organisations
 *
 * De même sur la vue des rubriques.
 *
 */
function contacts_afficher_contenu_objet($flux)
{
	if ($flux['args']['type'] == 'auteur') {

		if (lire_config('contacts_et_organisations/associer_aux_auteurs') and
			lire_config('contacts_et_organisations/afficher_infos_sur_auteurs')) {

			$id = intval($flux['args']['id_objet']);

			// informations sur le contact et ses liens
			if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id))
			{
				$infos = recuperer_fond('prive/squelettes/contenu/contact_sur_auteur', array('id_contact' => $id_contact));
				// FIXME: Cette suppression est osée !
				$infos = str_replace('ajax preload', 'ajax', $infos); // éviter un chargement ajax erroné. 
				$flux['data'] .= $infos;
			}
			// informations sur l'organisation et ses liens
			elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id))
			{
				$infos = recuperer_fond('prive/squelettes/contenu/organisation_sur_auteur', array('id_organisation' => $id_organisation));
				// FIXME: Cette suppression est osée !
				$infos = str_replace('ajax preload', 'ajax', $infos); // éviter un chargement ajax erroné. 
				$flux['data'] .= $infos;
			}
		}
	}

	if ($flux['args']['type'] == 'rubrique')
	{
		if (lire_config('contacts_et_organisations/lier_organisations_rubriques')) {
			$id = $flux['args']['id_objet'];
			$infos = recuperer_fond('prive/objets/editer/liens', array(
				'table_source'=>'organisations',
				'objet'=>'rubrique',
				'id_objet'=>$id,
				'editable'=>autoriser('associerorganisation', 'rubrique', $id) ? 'oui':'non'
			));
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
		if (lire_config('contacts_et_organisations/associer_aux_auteurs')) {
			$flux['data'] .= recuperer_fond('prive/squelettes/extra/selecteur_contacts_organisations', array(
				'id_auteur' => $flux['args']['id_auteur']
			));
		}
	}

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
