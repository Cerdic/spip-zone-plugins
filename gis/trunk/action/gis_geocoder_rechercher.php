<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/distant');

/**
 * Proxy vers le service du geocoder
 *
 * Cette fonction permet de transmettre une requete auprès du service
 * de recherche d'adresse d'OpenStreetMap (Nomatim ou Photon).
 *
 * Seuls les arguments spécifiques au service sont transmis.
 */
function action_gis_geocoder_rechercher_dist() {
	include_spip('inc/modifier');

	$mode = _request('mode');
	if (!$mode || !in_array($mode, array('search', 'reverse'))) {
		return;
	}

	/* On filtre les arguments à renvoyer à Nomatim (liste blanche) */
	$arguments = collecter_requests(array('format', 'q', 'limit', 'addressdetails', 'accept-language', 'lat', 'lon'), array());

	$geocoder = defined('_GIS_GEOCODER') ? _GIS_GEOCODER : 'photon';
	
	if ($geocoder == 'photon') {
		unset($arguments['format']);
		unset($arguments['addressdetails']);
	}

	if (!empty($arguments) && in_array($geocoder, array('photon','nominatim'))) {
		header('Content-Type: application/json; charset=UTF-8');
		if ($geocoder == 'photon') {
			if (isset($arguments['accept-language'])) {
				// ne garder que les deux premiers caractères du code de langue, car les variantes spipiennes comme fr_fem posent problème
				$arguments['lang'] = substr($arguments['accept-language'], 0, 2);
				unset($arguments['accept-language']);
			}
			if ($mode == 'search') {
				$mode = 'api/';
			} else {
				$mode = 'reverse';
			}
			$url = 'http://photon.komoot.de/';
		} else {
			$url = 'http://nominatim.openstreetmap.org/';
		}

		$url = defined('_GIS_GEOCODER_URL') ? _GIS_GEOCODER_URL : $url;
		$data = recuperer_page("{$url}{$mode}?" . http_build_query($arguments));
		echo $data;
	}
}
