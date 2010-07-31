<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_contact_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('/', $arg);

	// cas suppression d'une adresse et de toutes ses liaisons
	if ($arg[0] == 'contact' and intval($arg[1])) {
		action_supprimer_contact_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de l'adresse avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'organisation' and intval($arg[1])) {
		action_supprimer_organisation_post($arg[1]);
	}	

	else {
		spip_log("action_supprimer_contact_dist $arg pas compris");
	}
}

function action_supprimer_contact_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	sql_delete("spip_contacts", "id_auteur=" . sql_quote($id_auteur));
	sql_delete("spip_organisations_contacts", "id_contact=" . sql_quote($id_auteur));
	
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_auteur/$id_auteur'");
}

function action_supprimer_organisation_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	sql_delete("spip_organisations", "id_auteur=" . sql_quote($id_auteur));
	sql_delete("spip_organisations_contacts", "id_organisation=" . sql_quote($id_auteur));
	
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_auteur/$id_auteur'");
}

?>
