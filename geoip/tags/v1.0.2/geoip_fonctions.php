<?php
/**
 * GeoIP
 *
 * @plugin     GeoIP
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\geoip_fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Trouver le code pays par rapport à une IP
 *
 * @staticvar string $gi
 * @param string $ip
 * @return string
 */
function geoip_informations($ip, $fonction = 'geoip_country_code_by_addr') {

	// Utilise le module libapache2-geoip
	if (function_exists($fonction) and $_SERVER['GEOIP_ADDR']) {

		$resultat = $fonction($ip);

	} else {

		static $gi = null;
		if (is_null($ip)) {
			include_spip('lib/geoip');
			geoip_close($gi);
			return;
		}
		if (is_null($gi)) {
			include_spip('lib/geoip');
			$gi = geoip_open(find_in_path('lib/GeoIP.dat'), GEOIP_STANDARD);
		}

	}
	$resultat = $fonction($gi, $ip);

	return $resultat;

}
