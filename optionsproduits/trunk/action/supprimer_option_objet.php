<?php
/**
 * Utilisation de l'action supprimer pour l'objet option
 *
 * @plugin     Optionsproduits
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action pour supprimer un·e option
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
 **/
function action_supprimer_option_objet_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg              = $securiser_action();
	}
	// On récupère les infos de l'argument
	@list($objet, $id_objet, $id_option) = explode('-', $arg);

	include_spip('action/editer_liens');
	objet_dissocier(
		array('option' => $id_option),
		array($objet => $id_objet)
	);
}
