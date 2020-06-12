<?php
/**
 * Fonctions utiles au plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Enregistrer dans les métas l'objet à cadenasser
 *

 * @param string $objet
 *     sur quel objet faire l'action'
  * @param string $action
 *     ajouter ou enlever la meta pourcet objet
 *
**/
function action_lock_objet_dist() {

	include_spip('base/objets');
	include_spip('inc/config');

	$objet	= _request('objet');
	$action = _request('quoi');

	$cadenas = lire_config('lim/rubriques/cadenas', array());
	$table = table_objet_sql($objet);

	if ($action == 'add') {
		if (!in_array($table,  $cadenas)) {
			$cadenas[] = $table;
			ecrire_config('lim/rubriques/cadenas', $cadenas);
		}
	}

	if ($action == 'delete') {
		if (in_array($table,  $cadenas)) {
			$table = array($table);
			$cadenas = array_diff($cadenas, $table);
			ecrire_config('lim/rubriques/cadenas', $cadenas);
		}
	}

	spip_log("objet : ".$objet." action : ".$action, 'lim.' . _LOG_INFO);

}
