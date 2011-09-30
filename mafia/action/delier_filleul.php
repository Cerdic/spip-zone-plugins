<?php
/**
 * Plugin Filleuls & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_delier_filleul_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'une adresse et de toutes ses liaisons
	if (intval($arg[0]) and intval($arg[1])) {
		action_delier_filleul_post($arg[0], $arg[1]);
	}

	else {
		spip_log("action_delier_filleul_dist $arg pas compris");
	}
}

function action_delier_filleul_post($id_parrain,$id_filleul) {
	$id_filleul = intval($id_filleul);
	$id_parrain = intval($id_parrain);
	if ($id_filleul and $id_parrain) {
		sql_delete("spip_mafias", array(
			"id_filleul=" . sql_quote($id_filleul),
			"id_parrain=" . sql_quote($id_parrain),
		));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_filleul/$id_filleul'");
	}
}

?>
