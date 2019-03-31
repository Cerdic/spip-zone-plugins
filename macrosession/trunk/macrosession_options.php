<?php

/**
 * Outils SPIP supplémentaires pour une gestion efficace des accés aux données de la _session courant
 * et pour l'accès à des données de session étendue
 * 
 * Balises #_SESSION, #_SESSION_SI, #_SESSION_SINON, #_SESSION_FIN
 * #_AUTORISER_SI, #_AUTORISER_SINON, #_AUTORISER_FIN
 *
 * @copyright	2016, 2017, 2018, 2019
 * @author 		JLuc
 * @credit		Marcimat
 * @licence		GPL
 * 
 */

include_spip('inc/session');
include_spip ('inc/filtres');
include_spip('inc/autoriser');

// on utilise nobreak quand il n'y a pas de break entre 2 cases d'un switch,
// pour témoigner du fait que cette omission est intentionnelle
if (!defined('nobreak'))
	define('nobreak', '');
define ('V_OUVRE_PHP', "'<'.'" . '?php ');
define ('V_FERME_PHP', ' ?' . "'.'>'");

include_spip('inc/macrosession_utils');
include_spip('inc/_session');
include_spip('inc/_autoriser');

if (!function_exists('debug_get_mode')) {
	/**
	 * @param string $part : fonctionnalité testée
	 * @return bool : si l'argument debug est passé et égal à la fonctionnalité testée
	 * exemple : if (debug_get_mode('facteur')) echo $expediteur;
	 */
	function debug_get_mode($part = '')
	{
		return isset($_GET['debug'])
			and (!$part or ($_GET['debug'] == $part));
	}
}