<?php

/**
 * Plugin  Contacts & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_definir_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// Si on d�fini un contact
	if ($arg[0] == 'contact' and intval($arg[1])) {
		return action_definir_contact_post($arg[1]);
	}
	// Si on d�fini une organisation
	elseif ($arg[0] == 'organisation' and intval($arg[1])) {
		return action_definir_organisation_post($arg[1]);
	}	
	// Sinon �a veut rien dire
	else {
		spip_log("action_definir_contact_dist $arg pas compris");
	}
}

function action_definir_contact_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	return insert_contact($id_auteur);
}

function action_definir_organisation_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	return insert_organisation($id_auteur);
}


function insert_contact($id_auteur=0) {
	// r�cup�rer le nom
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);	

	$champs = array(
		'nom' => $nom,
		'id_auteur' => $id_auteur
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_contacts',
		),
		'data' => $champs
	));

	$id_contact = sql_insertq("spip_contacts", $champs);
	return $id_contact;
}

function insert_organisation($id_auteur=0) {

	// r�cup�rer le nom
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);	

	$champs = array(
		'nom' => $nom,
		'id_auteur' => $id_auteur
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_organisations',
		),
		'data' => $champs
	));

	$id_organisation = sql_insertq("spip_organisations", $champs);
	return $id_organisation;
}


?>
