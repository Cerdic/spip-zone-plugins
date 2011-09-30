<?php
/**
 * Plugin organisations & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_filleul_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$args = explode('/', $arg);

	// cas liaison id_parrain/id_filleul
	if (intval($args[0]) and is_numeric($args[1])) {
		// spip_log("appel a l'action_lier_filleul_dist avec $arg[0] $arg[1] comme argument");
		action_lier_filleul_post($args[0], $args[1]);
	}

	else {
		spip_log("action_lier_filleul_dist $arg pas compris","mafia");
	}
}

function action_lier_filleul_post($id_parrain,$id_filleul) {
	$id_parrain = intval($id_parrain);
	$id_filleul = intval($id_filleul);
	
	if ($id_filleul && $id_parrain) {
			sql_insertq("spip_mafias", array(
				"id_parrain"   => $id_parrain,
				"id_filleul" => $id_filleul,
			));

		include_spip('inc/invalideur');
		suivre_invalideur("id='id_filleul/$id_filleul'");
	}
}

?>
