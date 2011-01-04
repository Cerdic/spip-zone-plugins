<?php
/*
 * Aller chercher le GeoCodage via Google
 *
 */

// En JSON > http://maps.google.com/maps/api/geocode/json?sensor=false&address=paris
function geocodageGoogleJson($address){
	
	include_spip('inc/distant');
	$geocodeResponse = 'http://maps.google.com/maps/api/geocode/json';
	$geocodeResponse = parametre_url($geocodeResponse, 'sensor', 'false', '&');
	$geocodeResponse = parametre_url($geocodeResponse, 'address', $address, '&');	
	$geocodeResponse = json_decode(recuperer_page($geocodeResponse));
	
	// La latitude est rangee la -> $geocodeResponse->results[0]->geometry->location->lat
	return $geocodeResponse;
}

// En XML > http://maps.google.com/maps/api/geocode/xml?sensor=false&address=paris
// Non utilisee, beaucoup moins lisible que le JSON, mais peut etre pourra etre utile
function geocodageGoogleXml($address){
	
	include_spip('inc/distant');
	include_spip('inc/xml');
	$geocodeResponse = 'http://maps.google.com/maps/api/geocode/xml';
	$geocodeResponse = parametre_url($geocodeResponse, 'sensor', 'false', '&');
	$geocodeResponse = parametre_url($geocodeResponse, 'address', $address, '&');

	$geocodeResponse = recuperer_page($geocodeResponse);
	$geocodeResponse = spip_xml_parse($geocodeResponse);

	// La latitude est rangee la -> $url['GeocodeResponse'][0]['result'][0]['geometry'][0]['location'][0]['lat'][0];
	return $geocodeResponse;
}
