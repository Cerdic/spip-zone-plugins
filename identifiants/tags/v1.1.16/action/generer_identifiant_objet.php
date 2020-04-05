<?php
/**
 * Générer (ou mettre à jour/supprimer) l'identifiant d'un objet
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action : générer (ou mettre à jour/supprimer) l'identifiant d'un objet
 *
 * @param string|null $arg
 *     objet, id_objet et identifiant séparés par le charatère "-"
 * @return void
 */
function action_generer_identifiant_objet_dist($arg = null) {

	if (!function_exists('maj_identifiant_objet')) {
		include_spip('identifiants_fonctions');
	}

	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($identifiant, $objet, $id_objet) = explode('-', $arg);
	if (
		$objet
		and $id_objet = intval($id_objet)
	) {
		maj_identifiant_objet($objet, $id_objet, $identifiant);
	}

}
