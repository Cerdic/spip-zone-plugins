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
 * @param boolean $version true/false (true pour la version 2)
 * @return string
 */
function geoip_code_by_addr($ip, $version = false) {

	// Utilise le module libapache2-geoip
	if ($_SERVER['GEOIP_ADDR'] and $version == false) {

		$resultat = geoip_country_code_by_name($ip);

	} elseif ($version == false) {
		// Utilise la version 1 pour false
		static $gi = null;
		if (is_null($ip)) {
			include_spip('lib/geoip-api-php-master/src/geoip');
			geoip_close($gi);
			return;
		}
		if (is_null($gi)) {
			include_spip('lib/geoip-api-php-master/src/geoip');
			$gi = geoip_open(find_in_path('lib/geoip-api-php-master/maxmind-db/GeoIP.dat'), GEOIP_STANDARD);
		}

		$resultat = geoip_country_code_by_addr($gi, $ip);

	} else {
		// Utilise la version 2
		include_spip('lib/geoip2/vendor/autoload');

		$reader = new GeoIp2\Database\Reader('../lib/geoip2/vendor/maxmind-db/GeoLite2-City.mmdb');
		$record = $reader->city($ip);

		$resultat = $record->country->isoCode; // 'FR

	}

	return $resultat;

}
