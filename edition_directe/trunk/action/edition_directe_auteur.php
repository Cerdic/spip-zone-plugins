<?php
/**
 * Action d'édition du plugin Edition_directe
 *
 * @plugin     Edition_directe
 * @copyright  2011 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Edition_directe\Action
 */

if (!defined("_ECRIRE_INC_VERSION"))
	return;

function action_edition_directe_auteur_dist() {
	include_spip('inc/session');

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_auteur = session_get('id_auteur');

	$prefs = session_get('prefs');

	list ($action, $objet) = explode('-', $arg);

	$prefs['edition_directe'][$objet] = $action;

	sql_updateq('spip_auteurs', array(
		'prefs' => serialize($prefs)
	), 'id_auteur=' . $id_auteur);
}
