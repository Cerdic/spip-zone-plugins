<?php

/**
 * Gestion de l'action `lier_organisation_auteur` 
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un contact ou une organisation
 * 
 * @param null|string $arg
 *     Couple `type/id` où `type` est le type d'objet (organisation ou contact)
 *     et `id` son identifiant. En absence utilise l'argument de l'action sécurisée.
**/
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

/**
 * Supprime un contact et ses liaisons
 *
 * @param int $id_contact
 *     Identifiant du contact
**/
function action_supprimer_contact_post($id_contact) {
	$id_contact = intval($id_contact);

	// supprimer l'auteur associé si demandé dans la configuration
	include_spip('inc/config');
	if (lire_config('contacts_et_organisations/supprimer_reciproquement_auteurs_et_contacts')) {
		$id_auteur = sql_getfetsel("id_auteur", "spip_contacts", "id_contact=" . sql_quote($id_contact));
		if ($id_auteur) {
			include_spip('action/editer_objet');
			autoriser_exception('modifier', 'auteur', $id_auteur);
			objet_modifier('auteur', $id_auteur, array("statut" => "5poubelle"));
			autoriser_exception('modifier', 'auteur', $id_auteur, false);
		}
	}

	sql_delete("spip_contacts_liens", "id_contact=" . sql_quote($id_contact));
	sql_delete("spip_contacts", "id_contact=" . sql_quote($id_contact));
	sql_delete("spip_organisations_contacts", "id_contact=" . sql_quote($id_contact));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_contact/$id_contact'");
}

/**
 * Supprime une organisation et ses liaisons
 *
 * @param int $id_organisation
 *     Identifiant de l'organisation
**/
function action_supprimer_organisation_post($id_organisation) {
	$id_organisation = intval($id_organisation);

	// supprimer l'auteur associé si demandé dans la configuration
	include_spip('inc/config');
	if (lire_config('contacts_et_organisations/supprimer_reciproquement_auteurs_et_contacts')) {
		$id_auteur = sql_getfetsel("id_auteur", "spip_organisations", "id_organisation=" . sql_quote($id_organisation));
		if ($id_auteur) {
			include_spip('action/editer_objet');
			autoriser_exception('modifier', 'auteur', $id_auteur);
			objet_modifier('auteur', $id_auteur, array("statut" => "5poubelle"));
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
