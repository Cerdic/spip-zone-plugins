<?php
/**
 * Gestion de l'action `lier_contact_auteur`
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour lier un contact à un auteur
 *
 * @param null|string $arg
 *     Couple `id_contact/id_auteur` tel que `8/3`
 *     En absence utilise l'argument de l'action sécurisée.
**/
function action_lier_contact_auteur_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$args = explode('/', $arg);

	// cas liaison id_contact / id_auteur
	if (intval($args[0]) and is_numeric($args[1])) {
		// spip_log("appel à l'action_lier_contact_auteur_dist avec $arg[0] $arg[1] comme argument");
		action_lier_contact_auteur_post($args[0], $args[1]);
	}

	else {
		spip_log("action_lier_contact_auteur_dist $arg pas compris");
	}
}

/**
 * Lie un contact à un auteur
 *
 * @param int $id_contact
 *     Identifiant du contact
 * @param int $id_auteur
 *     Identifiant de l'auteur
 */
function action_lier_contact_auteur_post($id_contact, $id_auteur) {

	$id_auteur = intval($id_auteur); // id_auteur peut valoir 0 pour une deliaison
	$id_contact = intval($id_contact);
	if ($id_contact) {
		sql_updateq('spip_contacts', array('id_auteur' => $id_auteur), 'id_contact=' . $id_contact);

		include_spip('inc/invalideur');
		suivre_invalideur("id='id_contact/$id_contact'");
	}
}

?>
