<?php
/**
 * Action : supprimer un logo historique (qui n'est pas un document)
 *
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Roles_documents\Action
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Supprimer un logo historique (qui n'est pas un document)
 *
 * @uses logo_supprimer()
 *
 * @param $arg string
 *     Arguments séparés par un tiret
 *     sous la forme `$objet-$id_objet-$etat`
 *
 *     - objet    : type d'objet
 *     - id_objet : identifiant de l'objet
 *     - etat     : on ou off
 * @return void
 */
function action_supprimer_logo_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($objet, $id_objet, $etat) = explode('/', $arg);

	// Suppression
	include_spip('inc/autoriser');
	if (autoriser('iconifier', $objet, $id_objet)) {
		include_spip('action/editer_logo');
		logo_supprimer($objet, $id_objet, $etat);
	}
}
