<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
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

function action_supprimer_contact_post($id_contact) {
	$id_contact = intval($id_contact);

	// supprimer l'auteur associé si demandé dans la configuration
	if (CONTACTS_SUPPRESSIONS_RECIPROQUES_AVEC_AUTEURS) {
		$id_auteur = sql_getfetsel("id_auteur", "spip_contacts", "id_contact=" . sql_quote($id_contact));
		if ($id_auteur) {
			include_spip('action/editer_auteur');
			autoriser_exception('modifier', 'auteur', $id_auteur);
			auteurs_set($id_auteur, array("statut" => "5poubelle"));
			autoriser_exception('modifier', 'auteur', $id_auteur, false);
		}
	}

	sql_delete("spip_contacts_liens", "id_contact=" . sql_quote($id_contact));	
	sql_delete("spip_contacts", "id_contact=" . sql_quote($id_contact));
	sql_delete("spip_organisations_contacts", "id_contact=" . sql_quote($id_contact));
	
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_contact/$id_contact'");
}

function action_supprimer_organisation_post($id_organisation) {
	$id_organisation = intval($id_organisation);

	// supprimer l'auteur associé si demandé dans la configuration
	if (CONTACTS_SUPPRESSIONS_RECIPROQUES_AVEC_AUTEURS) {
		$id_auteur = sql_getfetsel("id_auteur", "spip_organisations", "id_organisation=" . sql_quote($id_organisation));
		if ($id_auteur) {
			include_spip('action/editer_auteur');
			autoriser_exception('modifier', 'auteur', $id_auteur);
			auteurs_set($id_auteur, array("statut" => "5poubelle"));
			autoriser_exception('modifier', 'auteur', $id_auteur, false);
		}
	}

	sql_delete("spip_organisations_liens", "id_organisation=" . sql_quote($id_organisation));	
	sql_delete("spip_organisations", "id_organisation=" . sql_quote($id_organisation));
	sql_delete("spip_organisations_contacts", "id_organisation=" . sql_quote($id_organisation));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_organisation/$id_organisation'");
}

?>
