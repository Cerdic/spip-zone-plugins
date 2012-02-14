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

	// Si on défini un contact
	if ($arg[0] == 'contact' and intval($arg[1])) {
		return action_definir_contact_post($arg[1]);
	}
	// Si on défini une organisation
	elseif ($arg[0] == 'organisation' and intval($arg[1])) {
		return action_definir_organisation_post($arg[1]);
	}	
	// Sinon ça veut rien dire
	else {
		spip_log("action_definir_contact_dist $arg pas compris");
	}
}

function action_definir_contact_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	include_spip('action/editer_contact');
	return contact_inserer(array(
		'id_auteur' => $id_auteur,
		'nom' => $nom,
	));
}

function action_definir_organisation_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	include_spip('action/editer_organisation');
	return organisation_inserer(array(
		'id_auteur' => $id_auteur,
		'nom' => $nom,
	));
}



?>
