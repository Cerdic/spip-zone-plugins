<?php
/**
 * Utilisation de l'action supprimer pour l'objet vecteur
 *
 * @plugin     jQuery Vector Maps
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Jqvmap\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Action pour supprimer un vecteur
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_vector_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete("spip_vectors",  "id_vector=" . sql_quote($arg));
		spip_log("Le vecteur $arg a été supprimé.");
	}
	else {
		spip_log("action_supprimer_vector_dist \$arg pas compris");
	}
}