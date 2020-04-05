<?php
/**
 * Définit les options du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!isset($GLOBALS['z_blocs'])) {
	$GLOBALS['z_blocs'] = array(
		'content',
		'aside',
		'extra',
		'head',
		'head_js',
		'header',
		'footer',
		'breadcrumb',
	);
}

if (!defined('_Z_AJAX_PARALLEL_LOAD')) {
	define('_Z_AJAX_PARALLEL_LOAD', 'extra');
}

if (!defined('_FICHIER_MAJ_PLUGINS')) {
	define('_FICHIER_MAJ_PLUGINS', _DIR_TMP . 'maj_sites_plugins.txt');
}

define('_SELECTEUR_GENERIQUE_ACTIVER_PUBLIC', true);
