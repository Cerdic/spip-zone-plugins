<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_delier_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'une adresse et de toutes ses liaisons
	if (intval($arg[0]) and intval($arg[1])) {
		action_delier_contact_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_delier_contact_dist $arg pas compris");
	}
}

function action_delier_contact_post($id_contact, $id_organisation) {
	$id_contact = intval($id_contact);
	$id_organisation = intval($id_organisation);
	if ($id_contact and $id_organisation) {
		sql_delete("spip_organisations_contacts", array(
			"id_contact=" . sql_quote($id_contact),
			"id_organisation=" . sql_quote($id_organisation),
		));
		//compatibilite
		sql_delete("spip_organisations_liens", array(
			"objet='contact'",
			"id_objet=" . sql_quote($id_contact),
			"id_organisation=" . sql_quote($id_organisation),
		));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_contact/$id_contact'");
	}
}

?>
