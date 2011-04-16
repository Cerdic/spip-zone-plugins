<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_organisation_auteur_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas liaison id_organisation / id_auteur
	spip_log($arg, 'c');
	if (intval($arg[0]) and is_numeric($arg[1])) {
		// spip_log("appel à l'action_lier_organisation_auteur_dist avec $arg[0] $arg[1] comme argument");
		action_lier_organisation_auteur_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_lier_organisation_auteur_dist $arg pas compris");
	}
}

function action_lier_organisation_auteur_post($id_organisation, $id_auteur) {

	$id_auteur = intval($id_auteur); // id_auteur peut valoir 0 pour une deliaison
	$id_organisation = intval($id_organisation);
	if ($id_organisation) {
		sql_updateq("spip_organisations", array(
			"id_auteur" => sql_quote($id_auteur)
		), "id_organisation=" . sql_quote($id_organisation));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_organisation/$id_organisation'");
	}
}

?>
