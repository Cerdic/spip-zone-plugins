<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'un contact
 * @param unknown_type $arg
 * @return unknown_type
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
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_contact
 */
function contact_inserer($champs=array()) {

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
 * Appelle la fonction de modification d'un contact
 *
 * @param int $id_contact
 * @param unknown_type $set
 * @return $err
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
