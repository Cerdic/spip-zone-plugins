<?php
/**
 * GeoIP
 *
 * @plugin     GeoIP
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\objets\contenu\test_geoip_fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('geoip_fonctions');

/**
 * Récupérer l'ip de l'utilisateur
 *
 * @return string
 */
function recuperer_ip_local() {

	$ip = $GLOBALS['ip'];
	
	return $ip;
}

/**
 * Récupérer le code pays par rapport à l'IP
 *
 * @param string $ip
 * @return string
 */
function geoIP_exist($ip) {

	$resultat = geoip_informations($ip);

	return $resultat;
}
