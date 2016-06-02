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
 * Récupérer le code pays par rapport à l'IP avec la version 1 ou 2
 *
 * @param boolean $version true/false (true pour la version 2) 
 * @return string
 */
function geoIP_exist($ip, $version = false) {

	$resultat = geoip_code_by_addr($ip, $version);

	return $resultat;
}
