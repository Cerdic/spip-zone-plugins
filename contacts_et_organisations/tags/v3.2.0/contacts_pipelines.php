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


/*
 * function contacts_ieconfig_metas
 *
 * export de configuration avec le plugin ieconfig
 * 
 * @param $table
 */

function contacts_ieconfig_metas($table) {
    $table['contacts']['titre'] = _T('contacts:contacts');
    $table['contacts']['icone'] = 'prive/themes/spip/images/contact-16.png';
    $table['contacts']['metas_serialize'] = 'contacts_et_organisations';
	
	return $table;
}

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
	  and isset($flux["args"]["id_objet"])
	  and $id = $flux["args"]["id_objet"]) {
		if (lire_config('contacts_et_organisations/associer_aux_auteurs')) {
			$id = intval($flux['args']['id_objet']);
			// cherchons un contact
			if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.intval($id))) {
				$flux['data'] .= '<br />' . recuperer_fond('prive/squelettes/hierarchie/contact', array('id_contact' => $id_contact));
			// sinon une organisation
			} elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.intval($id))) {
				$flux['data'] .= '<br />' . recuperer_fond('prive/squelettes/hierarchie/organisation', array('id_organisation' => $id_organisation));
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
function contacts_afficher_contenu_objet($flux) {
	include_spip('inc/config');

	if ($flux['args']['type'] == 'auteur') {
		if (lire_config('contacts_et_organisations/associer_aux_auteurs') and
			lire_config('contacts_et_organisations/afficher_infos_sur_auteurs')) {

			$id = intval($flux['args']['id_objet']);

			// informations sur le contact et ses liens
			if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur ='.$id))
			{
				$infos = recuperer_fond('prive/squelettes/contenu/contact_sur_auteur', array('id_contact' => $id_contact),array('ajax'=>true));
				$flux['data'] .= $infos;
			}
			// informations sur l'organisation et ses liens
			elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur ='.$id))
			{
				$infos = recuperer_fond('prive/squelettes/contenu/organisation_sur_auteur', array('id_organisation' => $id_organisation),array('ajax'=>true));
				$flux['data'] .= $infos;
			}
		}
	}

	// Ajouter un bloc de liaison avec les organisations sur les objets configurés pour ça
	if ($table = table_objet_sql($flux['args']['type']) and in_array($table, lire_config('contacts_et_organisations/lier_organisations_objets', array()))) {
		$id = $flux['args']['id_objet'];
		$infos = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'organisations',
			'objet' => $flux['args']['type'],
			'id_objet' => $id,
			'editable' => autoriser('associerorganisation', $flux['args']['type'], $id) ? 'oui':'non'
		));
		$flux['data'] .= $infos;
	}

	// Ajouter un bloc de liaison avec les contacts sur les objets configurés pour ça
	if ($table = table_objet_sql($flux['args']['type']) and in_array($table, lire_config('contacts_et_organisations/lier_contacts_objets', array()))) {
		$id = $flux['args']['id_objet'];
		$infos = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'contacts',
			'objet' => $flux['args']['type'],
			'id_objet' => $id,
			'editable' => autoriser('associercontact', $flux['args']['type'], $id) ? 'oui':'non'
		));
		$flux['data'] .= $infos;
	}

	return $flux;
}

/**
 * Pipeline boite_infos pour afficher clairement quand un auteur est un CONTACT ou une ORGANISATION
 * @param $flux
 * @return mixed
 */
function contacts_boite_infos($flux){
	if ($flux['args']['type']=='auteur'
	  and $id_auteur = intval($flux['args']['id'])){
		$html = recuperer_fond('prive/objets/infos/auteur-contact-organisation', array(
						'id_auteur' => $id_auteur
					));

		if ($p = strpos($flux['data'], '</p>')
		  and $p = strpos($flux['data'], '<p>', $p)){
			$flux['data'] = substr_replace($flux['data'], $html , $p, 0);
		}
		else {
			$flux['data'] .= $html;
		}
	}
	return $flux;
}


/**
 * Utilisation du pipeline affiche gauche
 *
 * - Affichage du formulaire de choix Contact/Organisation
 *   qui permet de creer un contact ou une organisation a partir d'un auteur qui n'est ni l'un ni l'autre
 *
 * @pipeline affiche_gauche
 *
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
**/
function contacts_affiche_gauche($flux) {
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
function contacts_insert_head_css($flux) {
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
function contacts_optimiser_base_disparus($flux) {

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
			sql_delete("spip_organisations_liens", "objet=".sql_quote('contact')." AND id_objet=" . sql_quote($id_contact));
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
			$n++;
		}

		$flux['data'] += $n;
	}

	# supprimer les liens contacts_liens dont les contacts ont disparu
	$res = sql_select(
		"CL.id_contact",
		"spip_contacts_liens AS CL
			LEFT JOIN spip_contacts AS C
			ON CL.id_contact=C.id_contact",
		"C.id_contact IS NULL"
	);
	while ($row = sql_fetch($res)) {
		$id_contact = $row['id_contact'];
		sql_delete("spip_contacts_liens", "id_contact=" . sql_quote($id_contact));
	}

	# supprimer les liens organisations_liens dont les organisations ont disparues
	$res = sql_select(
		"OL.id_organisation",
		"spip_organisations_liens AS OL
			LEFT JOIN spip_organisations AS O
			ON OL.id_organisation=O.id_organisation",
		"O.id_organisation IS NULL"
	);
	while ($row = sql_fetch($res)) {
		$id_organisation = $row['id_organisation'];
		sql_delete("spip_organisations_liens", "id_organisation=" . sql_quote($id_organisation));
	}

	# supprimer les liens organisations_liens dont les contacts ont disparus
	$res = sql_select(
		"OL.id_objet",
		"spip_organisations_liens AS OL
			LEFT JOIN spip_contacts AS C
			ON (OL.id_objet=C.id_contact AND OL.objet='contact')",
		"OL.objet='contact' AND C.id_contact IS NULL"
	);
	while ($row = sql_fetch($res)) {
		$id_contact = $row['id_objet'];
		sql_delete("spip_organisations_liens", "objet='contact' AND id_objet=" . sql_quote($id_contact));
	}


	return $flux;
}


function contacts_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'editer_auteur') {
		if (isset($flux['args']['contexte']['id_contact'])) {
			$contexte = $flux['args']['contexte'];
			$contexte['prefixe'] = 'contact_';
			if (preg_match(",<(li|div)[^>]*editer_bio[^>]*>,Uims", $flux['data'], $m)) {
				$contexte['tag'] = $m[1];
				$p = strpos($flux['data'], $m[0]);
				$ins = recuperer_fond('formulaires/editer_auteur_contact', $contexte);
				$flux['data'] = substr_replace($flux['data'], $ins, $p, 0);
			}
		}
		if (isset($flux['args']['contexte']['id_organisation'])) {
			$contexte = $flux['args']['contexte'];
			$contexte['prefixe'] = 'organisation_';
			if (preg_match(",<(li|div)[^>]*editer_bio[^>]*>,Uims", $flux['data'], $m)) {
				$contexte['tag'] = $m[1];
				$p = strpos($flux['data'], $m[0]);
				$ins = recuperer_fond('formulaires/editer_auteur_organisation', $contexte);
				$flux['data'] = substr_replace($flux['data'], $ins, $p, 0);
			}
		}
	}
	return $flux;
}

function contacts_formulaire_charger($flux) {
	if ($flux['args']['form'] == 'editer_auteur'
		and isset($flux['data']['id_auteur'])
		and $id_auteur = intval($flux['data']['id_auteur'])
	  and contacts_edition_integree_auteur()){
		if ($contact = sql_fetsel('*','spip_contacts','id_auteur='.intval($id_auteur))){
			$flux['data']['id_contact'] = $contact['id_contact'];
			unset($contact['id_contact']);
			foreach($contact as $k=>$v){
				$flux['data']['contact_'.$k] = $v;
			}
		}
		elseif ($organisation = sql_fetsel('*','spip_organisations','id_auteur='.intval($id_auteur))){
			$flux['data']['id_organisation'] = $organisation['id_organisation'];
			unset($organisation['id_organisation']);
			foreach($organisation as $k=>$v){
				$flux['data']['organisation_'.$k] = $v;
			}
		}
	}
	return $flux;
}

function contacts_formulaire_verifier($flux) {
	if ($flux['args']['form'] == 'editer_auteur'
	  and $id_auteur = intval($flux['args']['args'][0])) {
		if ($id_contact = intval(_request('id_contact'))
		  and sql_countsel('spip_contacts','id_auteur='.intval($id_auteur).' AND id_contact='.intval($id_contact))) {
			if ($editer_contact_verifier = charger_fonction('editer_contact_verifier', 'inc', true)){
				$prefixe = 'contact_';
				$flux['data'] = array_merge($flux['data'], $editer_contact_verifier($id_contact, 0, $prefixe));
			}
		}
		elseif ($id_organisation = intval(_request('id_organisation'))
		  and sql_countsel('spip_organisations','id_auteur='.intval($id_auteur).' AND id_organisation='.intval($id_organisation))) {
			if ($editer_organisation_verifier = charger_fonction('editer_organisation_verifier', 'inc', true)){
				$prefixe = 'organisation_';
				$flux['data'] = array_merge($flux['data'], $editer_organisation_verifier($id_organisation, 0, $prefixe));
			}
		}
	}
	return $flux;
}

function contacts_formulaire_traiter($flux) {
	if ($flux['args']['form'] == 'editer_auteur'
	  and $id_auteur = intval($flux['data']['id_auteur'])){

		$prefixe = $objet = $id_objet = '';
		if ($id_contact = intval(_request('id_contact'))
		  and sql_countsel('spip_contacts','id_auteur='.intval($id_auteur).' AND id_contact='.intval($id_contact))) {
			$prefixe = 'contact_';
			$objet = 'contact';
			$id_objet = $id_contact;
		}
		elseif ($id_organisation = intval(_request('id_organisation'))
		  and sql_countsel('spip_organisations','id_auteur='.intval($id_auteur).' AND id_organisation='.intval($id_organisation))) {
			$prefixe = 'organisation_';
			$objet = 'organisation';
			$id_objet = $id_organisation;
		}
		if ($prefixe and $objet and $id_objet){
			$l = strlen($prefixe);
			foreach ($_REQUEST as $k=>$v) {
				if (strncmp($k, $prefixe , $l) !==0
					and strncmp($k, 'var_' , 4) !==0 ){
					set_request($k);
				}
			}
			foreach ($_REQUEST as $k=>$v) {
				if (strncmp($k, $prefixe , $l) ==0 ){
					set_request(substr($k,$l), $v);
				}
			}
			formulaires_editer_objet_traiter($objet, $id_objet, 0, 0, '');
		}
	}
	return $flux;
}
