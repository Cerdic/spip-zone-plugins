<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion, Nadine Pavot
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_rubrique_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas liaison id_rubrique / id_organisation
	if (intval($arg[0]) and is_numeric($arg[1])) {
		//spip_log("appel à l'action_lier_rubrique_dist avec $arg[0] $arg[1] comme argument");
		action_lier_rubrique_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_lier_rubrique_dist $arg pas compris");
	}
}

function action_lier_rubrique_post($id_rubrique, $id_organisation) {

	$id_rubrique = intval($id_rubrique);
	$id_organisation = intval($id_organisation);
	if ($id_rubrique && $id_organisation) {
		sql_replace("spip_organisations_liens", array(
			"id_organisation" 	=> $id_organisation,
			"id_objet"			=> $id_rubrique,
			"objet"				=> 'rubrique'
		));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_rubrique/$id_rubrique'");
	}
}

?>
