<?php
/**
 * Utilisation de l'action supprimer pour l'objet prestations_type
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer un·e prestations_type
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_prestations_type_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_prestations_types',  'id_prestations_type=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_prestations_type_dist $arg pas compris");
	}
}
