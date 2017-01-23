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
 * Récupérer les informations de géolocalisation d'une IP
 *
 * @param string $ip
 * @return string
 */
function geoip_informations($ip, $fonction = 'geoip_country_code_by_addr') {

	// Utilise le module libapache2-geoip
	if (function_exists($fonction) and isset($_SERVER['GEOIP_ADDR'])) {
		$resultat = $fonction($ip);
	} else {
		include_spip('lib/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Decoder');
		include_spip('lib/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/InvalidDatabaseException');
		include_spip('lib/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Metadata');
		include_spip('lib/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Util');
		include_spip('lib/vendor/maxmind-db/reader/src/MaxMind/Db/Reader');
		include_spip('lib/vendor/geoip2/geoip2/src/ProviderInterface');
		include_spip('lib/vendor/geoip2/geoip2/src/Compat/JsonSerializable');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/AbstractRecord');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/AbstractPlaceRecord');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Subdivision');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/MaxMind');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Postal');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Location');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Country');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/City');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Continent');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/Traits');
		include_spip('lib/vendor/geoip2/geoip2/src/Record/RepresentedCountry');
		include_spip('lib/vendor/geoip2/geoip2/src/Model/AbstractModel');
		include_spip('lib/vendor/geoip2/geoip2/src/Model/Country');
		include_spip('lib/vendor/geoip2/geoip2/src/Model/City');
		include_spip('lib/vendor/geoip2/geoip2/src/Database/Reader');

		$reader = new GeoIp2\Database\Reader(find_in_path('lib/GeoLite2-City.mmdb'));
		$record = $reader->city($ip);

		$resultat_iso = $record->country->isoCode; // 'US'
		$resultat_pays = $record->country->name; // 'United States'
		$resultat_country_iso_nom = $record->country->names[strtolower($record->country->isoCode)]; // '美国'
		
		$resultat_region = $record->mostSpecificSubdivision->name; // 'Minnesota'
		$resultat_iso_region = $record->mostSpecificSubdivision->isoCode; // 'MN'

		$resultat_ville = $record->city->name; // 'Minneapolis'

		$resultat_codepostal = $record->postal->code; // '55455'

		$resultat_lat = $record->location->latitude; // 44.9733
		$resultat_lon = $record->location->longitude; // -93.2323
		$resultat_tz = $record->location->timeZone; // Europe/Paris

		$resultat = array(
			$resultat_iso,
			$resultat_pays,
			$resultat_country_iso_nom,
			$resultat_region,
			$resultat_iso_region,
			$resultat_ville,
			$resultat_codepostal,
			$resultat_lat,
			$resultat_lon,
			$resultat_tz);
	}
	
	return $resultat;
}
