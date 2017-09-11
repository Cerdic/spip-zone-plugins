<?php
/**
 * Définir la date d’activité
 *
 * @plugin     Date de connexion
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_connexion\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Action pour définir la date d’activité
 *
 * L'argument attendu est `2017-12-10 12:20:17` (une date quoi !)
 *
 * @param null|string $arg
 *     Clé des arguments. En absence utilise l'argument
 *     de l'action sécurisée.
 * @return void
 */
function action_maj_date_activite_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');
	if ($id_auteur) {
		include_spip('action/editer_auteur');
		auteur_modifier($id_auteur, array('date_suivi_activite' => $arg));
	}
}