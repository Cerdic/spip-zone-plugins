<?php
/**
 * Plugin organisations & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_lier_organisation_auteur_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$args = explode('/', $arg);

	// cas liaison id_organisation / id_auteur
	if (intval($args[0]) and is_numeric($args[1])) {
		// spip_log("appel à l'action_lier_organisation_auteur_dist avec $arg[0] $arg[1] comme argument");
		action_lier_organisation_auteur_post($args[0], $args[1]);
	}

	else {
		spip_log("action_lier_organisation_auteur_dist $arg pas compris");
	}
}

function action_lier_organisation_auteur_post($id_organisation, $id_auteur) {

	$id_auteur = intval($id_auteur); // id_auteur peut valoir 0 pour une deliaison
	$id_organisation = intval($id_organisation);
	if ($id_organisation) {
		if (!$id_auteur) {
			sql_delete('spip_organisations_liens', array(
				'id_organisation=' . $id_organisation,
				'objet=' . sql_quote('auteur')));
		} else {
			sql_insertq("spip_organisations_liens", array(
				"objet"      => "auteur",
				"id_objet"   => $id_auteur,
				"id_organisation" => $id_organisation,
			));
		}

		include_spip('inc/invalideur');
		suivre_invalideur("id='id_organisation/$id_organisation'");
	}
}

?>
