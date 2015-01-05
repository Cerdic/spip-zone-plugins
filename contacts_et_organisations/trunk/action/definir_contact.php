<?php

/**
 * Gestion de l'action `definir_contact`
 *
 * Crée et lie un contact ou une organisation à un auteur
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action qui crée une organisation ou un contact lié à l'auteur indiqué
 *
 * Le couple `type/id` (comme `organisation/8`) est donné en paramètre de
 * cette fonction ou en argument de l'action sécurisée et indique sur
 * quoi est lié l'auteur ainsi que l'identifiant de l'auteur
 *
 * @param null|string $arg
 *     Couple `type/id` où `type` est le type d'objet (organisation ou contact)
 *     et `id` l'identifiant de l'auteur. En absence utilise l'argument de l'action sécurisée.
 * @return void|int
 *     - Identifiant de l'organisation ou du contact venant d'être créé en cas de succès
 *     - Rien sinon
**/
function action_definir_contact_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// Si on défini un contact
	if ($arg[0] == 'contact' and intval($arg[1])) {
		return action_definir_contact_post($arg[1]);
	}
	// Si on défini une organisation
	elseif ($arg[0] == 'organisation' and intval($arg[1])) {
		return action_definir_organisation_post($arg[1]);
	}
	// Sinon ça veut rien dire
	else {
		spip_log("action_definir_contact_dist $arg pas compris");
	}
}

/**
 * Crée un contact lié à un auteur indiqué
 *
 * @param int $id_auteur
 *     Identifiant de l'auteur
 * @return int
 *     Identifiant du contact venant d'être créé
**/
function action_definir_contact_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	include_spip('action/editer_contact');
	return contact_inserer(null, array(
		'id_auteur' => $id_auteur,
		'nom' => $nom,
	));
}

/**
 * Crée une organisation liée à un auteur indiqué
 *
 * @param int $id_auteur
 *     Identifiant de l'auteur
 * @return int
 *     Identifiant de l'organisation venant d'être créée
**/
function action_definir_organisation_post($id_auteur) {
	$id_auteur = intval($id_auteur);
	$nom = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	include_spip('action/editer_organisation');
	return organisation_inserer(null, array(
		'id_auteur' => $id_auteur,
		'nom' => $nom,
	));
}



?>
