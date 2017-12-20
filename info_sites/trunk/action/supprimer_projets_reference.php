<?php
/**
 * Utilisation de l'action supprimer pour l'objet projets_reference
 *
 * @plugin     Références de projets
 * @copyright  2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_references\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e projets_reference
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
 **/
function action_supprimer_projets_reference_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_projets_references',  'id_projets_reference=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_projets_reference_dist $arg pas compris", 'info_sites');
	}
}
