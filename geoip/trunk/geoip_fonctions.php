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
 * Installation des base de données de Maxmind
 *
 * @param string $version
 * @return string
 */
function installer_databases_geoip($version) {

	include_spip('inc/distant');

	if ($version == 1) {
		$archive = _SRC_LIB_GEOIP_DB;
		$dest = $_SERVER['DOCUMENT_ROOT'] . '/' . _DIR_LIB_GEOIP_DB;
		$nom_fichier = _FILENAME_GEOIP_DB;
	}
	if ($version == 2) {
		$archive = _SRC_LIB_GEOIP2_DB;
		$dest = $_SERVER['DOCUMENT_ROOT'] . '/' . _DIR_LIB_GEOIP2_DB;
		$nom_fichier = _FILENAME_GEOIP2_DB;
	}

	if (!is_dir($dest)) {
		mkdir($dest, 0755);
	}

	$base = copie_locale($archive);

	// On décompresse le fichier et on le place au bonne endroit
	$lirefichier = fopen(find_in_path($base), 'rb');
	$gzopen = gzopen(find_in_path($base), 'rb');
	$contents = gzread($gzopen, 100000000);
	gzclose($gzopen);
	$fp = fopen($dest . $nom_fichier, 'wb');
	fwrite($fp, $contents);
	fclose($fp);

	return false;
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
			include_spip(find_in_path(_DIR_LIB_GEOPHP . '/src/geoip'));
			geoip_close($gi);
			return;
		}
		if (is_null($gi)) {
			// include_spip(find_in_path(_DIR_LIB_GEOPHP . '/src/geoip'));
			// $gi = geoip_open(find_in_path(_DIR_LIB_GEOIP_DB . _FILENAME_GEOIP_DB), GEOIP_STANDARD);

			include_spip('lib/geoip-api-php/src/geoip');
			$gi = geoip_open(find_in_path('lib/geoip-api-php/maxmind-db/GeoIP.dat'), GEOIP_STANDARD);
		}

		$resultat = geoip_country_code_by_addr($gi, $ip);

	} else {
		// Utilise la version 2
		// include_spip(_DIR_LIB_GEOPHP2 . '/src/Database/Reader');
		// $reader = new GeoIp2\Database\Reader('../' . _DIR_LIB_GEOIP2_DB . _FILENAME_GEOIP2_DB);
		include_spip('../lib/MaxMind-DB-Reader-php/src/MaxMind/Db/Reader/Decoder');
		include_spip('../lib/MaxMind-DB-Reader-php/src/MaxMind/Db/Reader/InvalidDatabaseException');
		include_spip('../lib/MaxMind-DB-Reader-php/src/MaxMind/Db/Reader/Metadata');
		include_spip('../lib/MaxMind-DB-Reader-php/src/MaxMind/Db/Reader/Util');
		include_spip('../lib/MaxMind-DB-Reader-php/src/MaxMind/Db/Reader');
		include_spip('../lib/GeoIP2-php/src/ProviderInterface');
		include_spip('../lib/GeoIP2-php/src/Compat/JsonSerializable');
		include_spip('../lib/GeoIP2-php/src/Record/AbstractRecord');
		include_spip('../lib/GeoIP2-php/src/Record/AbstractPlaceRecord');
		include_spip('../lib/GeoIP2-php/src/Record/MaxMind');
		include_spip('../lib/GeoIP2-php/src/Record/Postal');
		include_spip('../lib/GeoIP2-php/src/Record/Location');
		include_spip('../lib/GeoIP2-php/src/Record/Country');
		include_spip('../lib/GeoIP2-php/src/Record/City');
		include_spip('../lib/GeoIP2-php/src/Record/Continent');
		include_spip('../lib/GeoIP2-php/src/Record/Traits');
		include_spip('../lib/GeoIP2-php/src/Record/RepresentedCountry');
		include_spip('../lib/GeoIP2-php/src/Model/AbstractModel');
		include_spip('../lib/GeoIP2-php/src/Model/Country');
		include_spip('../lib/GeoIP2-php/src/Model/City');
		include_spip('../lib/GeoIP2-php/src/Database/Reader');
		
		$reader = new GeoIp2\Database\Reader('../lib/GeoIP2-php/maxmind-db/GeoLite2-City.mmdb');
		$record = $reader->city($ip);

		$resultat = $record->country->isoCode;

	}

	return $resultat;

}
