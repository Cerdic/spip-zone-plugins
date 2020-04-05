<?php

/**
 * Autorisations pour Xiti
 *
 * @plugin Xiti
 * @copyright  2014-2016
 * @author France diplomatie - Vincent
 * @license	GNU/GPL
 * @package	SPIP\Xiti\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function xiti_autoriser(){
}

function autoriser_xitiniveau_creer_dist($faire, $quoi, $id, $qui, $opts) {
	if (!function_exists('lire_config')) {
		inclure_spip('inc/config');
	}

	return lire_config('xiti/niveaux_deux') == 'on' and $qui['statut'] == '0minirezo';
}

function autoriser_xitiniveau_modifier_dist($faire, $quoi, $id, $qui, $opts) {
	return autoriser('creer', 'xitiniveau', $id, $qui, $opts);
}

function autoriser_xitiniveau_supprimer_dist($faire, $quoi, $id, $qui, $opts) {
	if (sql_getfetsel('id_objet', 'spip_xiti_niveaux_liens', 'id_xiti_niveau='.intval($id))) {
		return false;
	}
	return lire_config('xiti/niveaux_deux') == 'on' and $qui['statut'] == '0minirezo';
}

function autoriser_xitiniveaux_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'xitiniveau', $id, $qui, $opt);
}

/**
 * On ne peut joindre ou délier un niveau xiti qu'a un objet qu'on a le droit d'editer
 * Il faut aussi que les niveaux xiti aient ete actives sur les objets concernes
 *
 * @return bool
 */
function autoriser_lierxitiniveau_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	return (in_array(table_objet_sql($type), lire_config('xiti/xiti_niveaux_objets', array())))
		and (($id > 0 and autoriser('modifier', $type, $id, $qui, $opt)));
}

function autoriser_dissocierxitiniveau_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('lierxitiniveau', $type, $id, $qui, $opt);
}
