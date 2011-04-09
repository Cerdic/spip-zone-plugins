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

	// cas suppression d'une adresse et de toutes ses liaisons
	if ($arg[0] == 'contact' and intval($arg[1])) {
		action_definir_contact_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de l'adresse avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'organisation' and intval($arg[1])) {
		action_definir_organisation_post($arg[1]);
	}	

	else {
		spip_log("action_definir_contact_dist $arg pas compris");
	}
}

function action_definir_contact_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	insert_contact($id_auteur);
}

function action_definir_organisation_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	insert_organisation($id_auteur);
}


function insert_contact($id_auteur=0) {
	// récupérer le nom
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);	

	$champs = array(
		'nom' => $nom
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_contacts',
		),
		'data' => $champs
	));

	$id_contact = sql_insertq("spip_contacts", $champs);
	sql_insertq('spip_contacts_liens',array('id_objet' => $id_auteur,'objet' => 'auteur',"id_contact"=>$id_contact));
	return $id_contact;
}

function insert_organisation($id_auteur=0) {

	// récupérer le nom
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);	

	$champs = array(
		'id_auteur' => $id_auteur,
		'nom' => $nom
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
