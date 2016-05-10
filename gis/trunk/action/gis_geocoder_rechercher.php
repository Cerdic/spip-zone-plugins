<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/distant');

/**
 * Proxy vers le service Nomatim d'OpenStreetMap.
 *
 * Cette fonction permet de transmettre une requete auprès du service
 * de recherche d'adresse d'OpenStreetMap (Nomatim).
 *
 * Seuls les arguments spécifiques à Nomatim sont transmis.
 */
function action_gis_geocoder_rechercher_dist() {
	include_spip('inc/modifier');

	$mode = _request('mode');
	if (!$mode || !in_array($mode, array('search', 'reverse'))) {
		return;
	}

	/* On filtre les arguments à renvoyer à Nomatim (liste blanche) */
	$arguments = collecter_requests(array('json_callback', 'format', 'q', 'limit', 'addressdetails', 'accept-language', 'lat', 'lon'), array());

	$geocoder = defined('_GIS_GEOCODER') ? _GIS_GEOCODER : 'nominatim';

	if (!empty($arguments) && in_array($geocoder, array('photon','nominatim'))) {
		header('Content-Type: application/json; charset=UTF-8');
		if ($geocoder == 'photon') {
			if (isset($arguments['accept-language'])) {
				$arguments['lang'] = $arguments['accept-language'];
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
