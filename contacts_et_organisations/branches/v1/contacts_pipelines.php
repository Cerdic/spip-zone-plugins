<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */


/**
 *
 * JQuery pour afficher fil d'ariane
 * du contact ou de l'organisation
 * sur la vue de l'auteur
 * le pipeline affiche_hierarchie ne marche pas
 */
function contacts_header_prive($flux)
{
	$flux .= '<script type="text/javascript">';
	$flux .= '$(document).ready(function(){';
	$flux .= 'if ($("#ariane").length>0) {';
	$flux .= '	$("#page").prepend($("#ariane").html());';
	$flux .= '}});';
	$flux .= '</script>';

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
	if ($flux["args"]["type"] == "auteur") {
		$id = $flux["args"]["id_objet"];
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', array('id_auteur=' . intval($id)));
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', array('id_auteur=' . intval($id)));

		if ($id_contact || $id_organisation)
		{
			$infos = '';
			$bouton_edit = '';
			$self = generer_url_ecrire('auteur_infos', 'id_auteur='.$id, '&', true);

			if ($id_contact)
			{
				// informations du contact
				$infos  = recuperer_fond('prive/contenu/contact', array('id_contact' => $id_contact));

				// bouton "Modifier le contact"
				if ( autoriser('modifier', 'contact', $id_contact) )
				{
					$texte = _T('contacts:contact_editer');
					$lien = parametre_url(generer_url_ecrire('contact_edit', 'id_contact='.$id_contact), 'redirect' , $self);
					$fond = find_in_path('images/co_contact-24.png');
					$bouton_edit = icone_inline($texte, $lien, $fond, '', 'right') . '<br class="nettoyeur" />' ;
				}
			}

			else if ($id_organisation)
			{
				// informations de l'organisation
				$infos = recuperer_fond('prive/contenu/organisation', array('id_organisation' => $id_organisation));

				// bouton "Modifier l'organisation"
				if ( autoriser('modifier', 'organisation', $id_organisation) )
				{
					$texte = _T('contacts:organisation_editer');
					$lien = parametre_url(generer_url_ecrire('organisation_edit', 'id_organisation='.$id_organisation), 'redirect' , $self);
					$fond = find_in_path('images/co_organisation-24.png');
					$bouton_edit = icone_inline($texte, $lien, $fond, '', 'right') . '<br class="nettoyeur" />' ;
				}
			}

			$flux['data'] = $bouton_edit . $infos . $flux['data'] ;

		} // fin fiche contact ou organisation
	}

	return $flux;
}


function contacts_boite_infos($flux){
	/*
	if ($flux['args']['type'] == 'contact'){

		$id_auteur = sql_getfetsel('id_objet',
		'spip_contacts_liens',
		'objet=\'auteur\' AND id_contact=' . intval($flux['args']['id_contact']));

		if ( $id_auteur > 0 )
		{
			$auteur = sql_fetsel("*", "spip_auteurs", "id_auteur=$id_auteur");

			$flux['data']			.= '<div>toto</div>';
		}
	}

	$flux['data']			=  $flux['data'] ;
	*/
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

	if ($flux['args']['exec'] == 'auteur_infos'
		and $id = $flux["args"]["id_auteur"]) {

		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur=' . intval($id));
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur=' . intval($id));

		if ($id_contact || $id_organisation)
		{
			$self = generer_url_ecrire('auteur_infos', 'id_auteur='.$id, '&', true);

			// boîte selection de contacts ou d'organisations liés
			$flux['data'] .= recuperer_fond('prive/boite/selecteur_contacts_organisations',
						 array('id_auteur'=>$id), array('ajax'=>true));

			if ($id_contact)
			{
				// fil d'ariane du contact
				$contact = sql_fetsel('nom, prenom', 'spip_contacts', 'id_contact='.$id_contact);
				$flux['data'] .= recuperer_fond('prive/boite/ariane_contact', array(
						'nom'				=> $contact['nom'],
						'prenom'			=> $contact['prenom']
					));
			} // fin 'si contact'

			else if ($id_organisation)
			{

				// bouton "Créer un contact"
				if ( autoriser('creer', 'contact') )
				{
					$texte = _T('contacts:contact_creer');
					$lien = parametre_url(generer_url_ecrire('contact_edit', 'new=oui&id_organisation='.$id_organisation), 'redirect', $self);
					$fond = find_in_path('images/co_contact-24.png');
					$flux['data'] .= icone($texte, $lien, $fond, '', 'right') ;
				}

				// fil d'ariane de l'organisation
				$flux['data'] .= recuperer_fond('prive/boite/ariane_organisation', array(
						'id_organisation' => $id_organisation
					));
			}// fin 'si organisation'
		} else {
			$flux['data'] .= recuperer_fond('prive/boite/selecteur_contacts_organisations', array(
								'id_auteur'=>$flux['args']['id_auteur']
								));
		}
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
	if ($flux['args']['exec'] == 'auteur_infos')
	{

		$id = $flux["args"]["id_auteur"];
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur=' . intval($id));
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur=' . intval($id));

		if ( $id_contact || $id_organisation )
		{
			$ajout = '';
			$porfolio_documents = '';
			$self = generer_url_ecrire('auteur_infos', 'id_auteur='.$id,'&',true);

			if ($id_contact)
			{
				// liste des organisations auxquelles est lié le contact
				$ajout .= recuperer_fond('prive/liste/linked_organisations',
					array(
						'id_contact'  => $id_contact,
						'titre'       => _T('contacts:info_organisations_appartenance')
					),
					array('ajax'      => true)
				);
			}


			else if ($id_organisation)
			{
				// liste des organisations filles
				$ajout  .= recuperer_fond('prive/liste/organisations',
					array(
						'id_parent' => $id_organisation,
						'titre'     => _T('contacts:info_organisations_filles')
					),
					array('ajax'    => true)
				);


				// bouton "Créer une organisation fille"
				if ( autoriser('creer', 'organisation') )
				{
					$texte = _T('contacts:organisation_creer_fille');
					$lien = parametre_url(generer_url_ecrire('organisation_edit', 'new=oui&id_parent='.$id_organisation), 'redirect', $self);
					$fond = find_in_path('images/co_organisation-24.png');
					$ajout .= icone_inline($texte, $lien, $fond, '', 'right') . '<br class="nettoyeur">';
				}

				// liste des contacts liés à l'organisation
				$ajout  .= recuperer_fond('prive/liste/linked_contacts',
					array(
						'id_organisation'	=> $id_organisation,
						'titre'			=> _T('contacts:info_contacts_organisation')
					),
					array('ajax'		=> true)
				);
			}

			// portfolio documents
			$porfolio_documents = recuperer_fond('prive/contenu/portfolio_document',
							array(),
							array('ajax'		=> true));

			$flux['data'] = $ajout . $flux['data'] . $porfolio_documents  ;
		}// fin page contact ou organisation
	}

	if ($flux['args']['exec'] == 'naviguer' && $flux['args']['id_rubrique'])
	{
		$flux['data'] .= recuperer_fond('prive/liste/organisations_liees_rubrique', array(
			'id_rubrique' => $flux['args']['id_rubrique'],
			'titre' => _T('contacts:info_organisations_appartenance')
		), array('ajax'=>true));
	} // fin page rubrique

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
	$tables['organisation']['id_organisation'] = 12;
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

function contacts_declarer_url_objets($array){
	$array[] = 'organisation';
	$array[] = 'contact';
	return $array;
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
	if (CONTACTS_SUPPRESSIONS_RECIPROQUES_AVEC_AUTEURS) {
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
