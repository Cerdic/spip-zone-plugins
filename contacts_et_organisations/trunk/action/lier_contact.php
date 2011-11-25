<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas liaison id_contact / id_organisation
	if (intval($arg[0]) and intval($arg[1])) {
		// spip_log("appel à l'action_lier_contact_dist avec $arg[0] / $arg[1] comme argument");
		action_lier_contact_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_lier_contact_dist $arg pas compris");
	}
}

function action_lier_contact_post($id_contact, $id_organisation) {
	$id_contact = intval($id_contact);
	$id_organisation = intval($id_organisation);
	if ($id_contact && $id_organisation) {
		sql_insertq("spip_organisations_contacts", array(
			"id_contact" => sql_quote($id_contact),
			"id_organisation" => sql_quote($id_organisation)
		));
			
				//compatibilite
					$champs = array(
						'id_organisation' => sql_quote($id_organisation),
						'objet' => 'contact',
						'id_objet' => $id_contact
					);
					sql_insertq("spip_organisations_liens", $champs);

		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_contact/$id_contact'");
	}
}

?>
