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
