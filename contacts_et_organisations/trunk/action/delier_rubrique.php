<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud, Cyril Marion, Nadine Pavot
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_delier_rubrique_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	if (intval($arg[0]) and intval($arg[1])) {
		action_delier_rubrique_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_delier_rubrique_post $arg pas compris");
	}
}

function action_delier_rubrique_post($id_rubrique, $id_organisation) {
	$id_rubrique = intval($id_rubrique);
	$id_organisation = intval($id_organisation);
	if ($id_rubrique && $id_organisation) {	
		sql_delete("spip_organisations_liens", array(
			"id_organisation=" . sql_quote($id_organisation),
			"id_objet=" . sql_quote($id_rubrique),
			"objet='rubrique'"
		));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_rubrique/$id_rubrique'");
	}
}

?>
