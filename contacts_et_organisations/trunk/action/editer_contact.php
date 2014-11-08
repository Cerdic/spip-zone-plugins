<?php

/**
 * Gestion de l'action `editer_contact` et des fonctions d'insertion
 * et modification de contacts
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / modification d'un contact
 * 
 * @param null|int $arg
 *     Identifiant du contact.
 *     En absence utilise l'argument de l'action sécurisée.
 * @return array
 *     Liste (identifiant du contact, Texte d'erreur éventuel)
 */
function action_editer_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_contact n'est pas un nombre, c'est une creation
	if (!$id_contact = intval($arg)) {
		$id_contact = contact_inserer();
		// si parent d'organisation transmis, on le sauve
		if ($id_organisation = intval(_request('id_parent'))) {
			include_spip('action/editer_liens_simples');
			objet_associer_simples(
				array('organisation'=>$id_organisation),
				array('contact'=>$id_contact));
		}
	}

	// Enregistre l'envoi dans la BD
	if ($id_contact > 0) $err = contact_modifier($id_contact);

	return array($id_contact, $err);
}

/**
 * Crée un nouveau contact et retourne son ID
 *
 * @pipeline_appel pre_insertion
 * @pipeline_appel post_insertion
 * 
 * @param array $champs
 *     Un tableau avec les champs par défaut lors de l'insertion
 * @return int
 *     Identifiant du contact créé
 */
function contact_inserer($id_parent=null, $champs=array()) {

	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_contacts',
			),
			'data' => $champs
		)
	);
	
	// Insérer l'objet
	$id_contact = sql_insertq('spip_contacts', $champs);
	
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_contacts',
			),
			'data' => $champs
		)
	);
	
	return $id_contact;
}


/**
 * Modifie les données d'un contact
 *
 * Récupère les valeurs qui ont été postées d'un formulaire d'édition
 * automatiquement.
 * 
 * @param int $id_contact
 *     Identifiant du contact
 * @param null|array $set
 *     Couples de valeurs à affecter d'office
 * @return string
 *     Vide en cas de succès, texte d'erreur sinon.
 */
function contact_modifier($id_contact, $set=null) {

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('contact','champs_editables'),
		// black list
		array(),
		// donnees eventuellement fournies
		$set
	);

	if ($err = objet_modifier_champs('contact', $id_contact,
		array(
			'nonvide' => array('nom' => _T('contacts:contact_nouveau_titre')." "._T('info_numero_abbreviation').$id_contact),
		),
		$c)) {
		return $err;
	}

	return $err;
}


?>
