<?php
/**
 * Autorisations pour Xiti
 *
 * @plugin     Xiti
 * @copyright  2014-2017
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @license	GNU/GPL
 * @package	SPIP\Xiti\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function xiti_autoriser() {
}

/**
 * Autorisation de créer un Niveau 2
 *
 * Il faut que les niveaux deux soient activés
 * Seuls les administrateurs peuvent créer des niveaux deux
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_xitiniveau_creer_dist($faire, $quoi, $id, $qui, $opts) {
	if (!function_exists('lire_config')) {
		inclure_spip('inc/config');
	}

	return lire_config('xiti/niveaux_deux') == 'on' and $qui['statut'] == '0minirezo';
}


/**
 * Autorisation de modifier un Niveau 2
 *
 * Identique à celle de création
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_xitiniveau_modifier_dist($faire, $quoi, $id, $qui, $opts) {
	return autoriser('creer', 'xitiniveau', $id, $qui, $opts);
}

/**
 * Autorisation de voir les révisions d'un Niveau 2
 *
 * Identique à celle de création
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_xitiniveau_voirrevisions($faire, $quoi, $id, $qui, $opts) {
	return autoriser('creer', 'xitiniveau', $id, $qui, $opts);
}

/**
 * Autorisation de supprimer un Niveau 2
 *
 * Il faut que rien ne soit lié au niveau deux
 * Sinon, identique à celle de création
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_xitiniveau_supprimer_dist($faire, $quoi, $id, $qui, $opts) {
	if (sql_getfetsel('id_objet', 'spip_xiti_niveaux_liens', 'id_xiti_niveau='.intval($id))) {
		return false;
	}
	return lire_config('xiti/niveaux_deux') == 'on' and $qui['statut'] == '0minirezo';
}

/**
 * Autorisation d'accès au menu des niveaux 2
 *
 * Identique à celle de création
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_xitiniveaux_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'xitiniveau', $id, $qui, $opt);
}

/**
 * Autorisation de lier un niveau 2
 *
 * On ne peut joindre ou délier un niveau xiti qu'a un objet que l'on a le droit d'editer
 * Il faut aussi que les niveaux xiti aient ete actives sur les objets concernes
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_lierxitiniveau_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	return (in_array(table_objet_sql($type), lire_config('xiti/xiti_niveaux_objets', array())))
		and (($id > 0 and autoriser('modifier', $type, $id, $qui, $opt)));
}

/**
 * Autorisation de dissocier un niveau 2
 *
 * On ne peut joindre ou délier un niveau xiti qu'a un objet que l'on a le droit d'editer
 * Il faut aussi que les niveaux xiti aient ete actives sur les objets concernes
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param array $qui
 * @param array $opts
 * @return boolean
 */
function autoriser_dissocierxitiniveau_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('lierxitiniveau', $type, $id, $qui, $opt);
}
