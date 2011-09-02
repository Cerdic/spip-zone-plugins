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
		$id_contact = insert_contact();
		// si parent d'organisation transmis, on le sauve
		if ($id_organisation = intval(_request('id_parent'))) {
			$lier_contact = charger_fonction('lier_contact', 'action');
			$lier_contact("$id_contact/$id_organisation");
		}
	}

	// Enregistre l'envoi dans la BD
	if ($id_contact > 0) $err = contact_set($id_contact);

	return array($id_contact, $err);
}

/**
 * Crée un nouveau contact et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_organisation
 */
function insert_contact($champs=array()) {
	$id_contact = false;
	
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
 * Appelle la fonction de modification d'une organisation
 *
 * @param int $id_contact
 * @param unknown_type $set
 * @return $err
 */
function contact_set($id_contact, $set=null) {
	$err = '';
	
	$champs = array(
		'civilite', 'prenom', 'nom',
		'fonction', 'date_naissance',
		'descriptif'
	);
	
	$c = array();
	foreach ($champs as $champ)
		$c[$champ] = _request($champ,$set);

		
	include_spip('inc/modifier');
	revision_contact($id_contact, $c);
	
	return $err;
}

/**
 * Enregistre une révision de contact
 *
 * @param int $id_produit
 * @param array $c
 * @return
 */
function revision_contact($id_contact, $c=false) {
	$invalideur = "id='id_contact/$id_contact'";

	modifier_contenu('contact', $id_contact,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}


?>
