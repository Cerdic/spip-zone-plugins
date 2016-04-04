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
 * Action pour lier une organisation à un auteur
 * 
 * @param null|string $arg
 *     Couple `id_organisation/id_auteur` tel que `8/3`
 *     En absence utilise l'argument de l'action sécurisée.
**/
function action_lier_organisation_auteur_dist($arg = null) {
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

/**
 * Lie une organisation à un auteur
 * 
 * @param int $id_organisation
 *     Identifiant de l'organisation
 * @param int $id_auteur
 *     Identifiant de l'auteur
 */
function action_lier_organisation_auteur_post($id_organisation, $id_auteur) {

	$id_auteur = intval($id_auteur); // id_auteur peut valoir 0 pour une deliaison
	$id_organisation = intval($id_organisation);
	if ($id_organisation) {
		sql_updateq('spip_organisations', array('id_auteur' => $id_auteur), 'id_organisation=' . $id_organisation);

		include_spip('inc/invalideur');
		suivre_invalideur("id='id_organisation/$id_organisation'");
	}
}
