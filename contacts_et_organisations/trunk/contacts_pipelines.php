<?php

/**
 * Utilisations de pipelines
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Pipelines
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajoute un fil d'ariane sur les auteurs définis comme contacts ou organisation
 *
 * @pipeline affiche_hierarchie
 * 
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function contacts_affiche_hierarchie($flux)
{
	if ($flux['args']['objet'] == 'auteur'
	  and isset($flux["args"]["id_auteur"])
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
 * Utilisation du pipeline afficher_contenu_objet
 * 
 * - Insertion dans la vue des auteurs des informations relatives aux
 *   contacts et organisations
 * - Insertion sur les rubriques du choix des organisations
 *
 * @pipeline afficher_contenu_objet
 * 
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
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
				$flux['data'] .= $infos;
			}
			// informations sur l'organisation et ses liens
			elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id))
			{
				$infos = recuperer_fond('prive/squelettes/contenu/organisation_sur_auteur', array('id_organisation' => $id_organisation));
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
 * Utilisation du pipeline affiche gauche
 * 
 * - Affichage du formulaire de choix Contact/Organisation
 *   dans la colonne de vue d'un auteur
 * - Affichage du formulaire de recherche et de sélection d'Organisations
 *   dans la colonne de vue d'une rubrique
 *
 * @pipeline affiche_gauche
 * 
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
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
 *
 * @pipeline insert_head_css
 * 
 * @param string $flux
 *     Code HTML de chargement des CSS
 * @return string
 *     Code HTML de chargement des CSS
**/
function contacts_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('contacts.css').'" media="all" />';
	return $flux;
}




/**
 * Optimiser la base (suppression des contacts et organisations dont les auteurs liés ont disparu)
 *
 * Si la configuration du plugin indique que la suppression d'un auteur entraîne la suppression
 * de la fiche de contact, alors on supprime effectivement ce contact.
 * 
 * @param array $flux
 * @return array
 */
function contacts_optimiser_base_disparus($flux){

	// supprimer un contact associé à un auteur disparu si demandé dans la configuration
	include_spip('inc/config');
	if (lire_config('contacts_et_organisations/supprimer_reciproquement_auteurs_et_contacts')) {
		$n = 0;

		# supprimer les contacts dont les auteurs ont disparu
		$res = sql_select(
			"contacts.id_contact",
			"spip_contacts AS contacts
				LEFT JOIN spip_auteurs AS auteurs
				ON contacts.id_auteur=auteurs.id_auteur",
			array(
				"auteurs.id_auteur IS NULL",
				"contacts.id_auteur > 0"
			)
		);

		while ($row = sql_fetch($res)) {
			$id_contact = $row['id_contact'];
			sql_delete("spip_contacts_liens", "id_contact=" . sql_quote($id_contact));
			sql_delete("spip_contacts", "id_contact=" . sql_quote($id_contact));
			sql_delete("spip_organisations_contacts", "id_contact=" . sql_quote($id_contact));
			$n++;
		}

		# supprimer les organisations dont les auteurs ont disparu
		$res = sql_select(
			"organisations.id_organisation",
			"spip_organisations AS organisations
				LEFT JOIN spip_auteurs AS auteurs
				ON organisations.id_auteur=auteurs.id_auteur",
			array(
				"auteurs.id_auteur IS NULL",
				"organisations.id_auteur > 0"
			)
		);

		while ($row = sql_fetch($res)) {
			$id_organisation = $row['id_organisation'];
			sql_delete("spip_organisations_liens", "id_organisation=" . sql_quote($id_organisation));
			sql_delete("spip_organisations", "id_organisation=" . sql_quote($id_organisation));
			sql_delete("spip_organisations_contacts", "id_organisation=" . sql_quote($id_organisation));
			$n++;
		}

		$flux['data'] += $n;
	}

	return $flux;
}

?>
