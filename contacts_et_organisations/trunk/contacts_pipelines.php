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
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id);
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id);

		if ($id_contact or $id_organisation)
		{
			include_spip('inc/presentation'); // icone_verticale

			$infos = '';
			$bouton_edit = '';
			$self = self();

			if ($id_contact)
			{
				// informations du contact
				$infos  = recuperer_fond('prive/objets/contenu/contact', array('id_contact' => $id_contact));
				
				// bouton "Modifier le contact"
				if ( autoriser('modifier', 'contact', $id_contact) )
				{
					$texte = _T('contacts:contact_editer');
					$lien = parametre_url(generer_url_ecrire('contact_edit', 'id_contact='.$id_contact), 'redirect' , $self);
					$fond = chemin_image('contact-24.png');
					$bouton_edit = icone_verticale($texte, $lien, $fond, '', 'right');
				}
			}
			
			else if ($id_organisation)
			{
				// informations de l'organisation
				$infos = recuperer_fond('prive/objets/contenu/organisation', array('id_organisation' => $id_organisation));
				
				// bouton "Modifier l'organisation"
				if ( autoriser('modifier', 'organisation', $id_organisation) )
				{
					$texte = _T('contacts:organisation_editer');
					$lien = parametre_url(generer_url_ecrire('organisation_edit', 'id_organisation='.$id_organisation), 'redirect' , $self);
					$fond = chemin_image('organisation-24.png');
					$bouton_edit = icone_verticale($texte, $lien, $fond, '', 'right');
				}
			}

			$flux['data'] = $bouton_edit . $infos . $flux['data'] ;

		} // fin fiche contact ou organisation
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

		$id = intval($flux['args']['id_auteur']);
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.$id);
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur='.$id);

		if ($id_contact or $id_organisation)
		{
			include_spip('inc/presentation'); // icone_verticale
			$self = self();

			// boîte selection de contacts ou d'organisations liés
			$flux['data'] .= recuperer_fond(
				'prive/old/boite/selecteur_contacts_organisations',
				array('id_auteur' => $id), 
				array('ajax' => true)
			);
			
			if ($id_organisation) {
				// bouton "Créer un contact"
				if ( autoriser('creer', 'contact') )
				{
					$texte = _T('contacts:contact_creer');
					$lien = generer_url_ecrire('contact_edit', 'new=oui&id_organisation='.$id_organisation.'&redirect='.$self);
					$fond = chemin_image('contact-24.png');
					$flux['data'] .= icone_verticale($texte, $lien, $fond, '', 'right') ;
				}
			}
		} else {
			$flux['data'] .= recuperer_fond(
				'prive/old/boite/selecteur_contacts_organisations',
				array(
					'id_auteur' => $flux['args']['id_auteur'] 
				)
			); 
		}
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
 * Insertion dans la vue des auteurs
 * des informations relatives aux contacts et organisations
 * et
 * Insertion dans la vue des rubriques
 * des informations relatives aux organisations
 */
function contacts_affiche_milieu($flux){
	if ($flux['args']['exec'] == 'auteur')
	{
		$id = $flux["args"]["id_auteur"];
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur=' . intval($id));
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur=' . intval($id));

		if ( $id_contact || $id_organisation )
		{
			include_spip('inc/presentation'); // icone_verticale
			
			$ajout = '';
			$porfolio_documents = '';
			$self = generer_url_ecrire('auteur_infos', 'id_auteur='.$id);
			
			if ($id_contact)
			{
				// liste des organisations auxquelles est lié le contact
				$ajout  = recuperer_fond('prive/objets/liste/linked_organisations', 
								array(
									'id_contact'	=> $id_contact,
									'titre'			=> _T('contacts:info_organisations_appartenance')
								),
								array('ajax'		=> true));
					
			}
	
	
			else if ($id_organisation)
			{
				// liste des contacts liés à l'organisation
				$ajout  = recuperer_fond('prive/objets/liste/linked_contacts', 
								array(
									'id_organisation'	=> $id_organisation,
									'titre'			=> _T('contacts:info_contacts_organisation')
								),
								array('ajax'		=> true));
				
				// bouton "Créer une organisation fille"
				if ( autoriser('creer', 'organisation') )
				{
					$texte = _T('contacts:organisation_creer_fille');
					$lien = generer_url_ecrire('organisation_edit', 'new=oui&id_parent='.$id_organisation.'&redirect='.$self);
					$fond = chemin_image('organisation-24.png');
					$ajout = icone_verticale($texte, $lien, $fond, '', 'right') . '<br class="nettoyeur">'. $ajout ;
				}

			}

			if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
		        $flux['data'] = substr_replace($flux['data'],$ajout,$p,0);
		    else
				$flux['data'] = $ajout . $flux['data'];	
		}// fin page contact ou organisation
	} 
		
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
