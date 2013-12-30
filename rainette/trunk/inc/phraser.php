<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * @param string $url
 * @param string $utiliser_namespace
 * @return array
 */
function url2flux_xml($url, $utiliser_namespace='false') {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// Tranformation de la chaine xml reçue en tableau associatif
	$convertir = charger_fonction('simplexml_to_array', 'inc');
	$xml = $convertir(simplexml_load_string($flux), $utiliser_namespace);
	$xml = $xml['root'];

	return $xml;
}

/**
 * @param string $url
 * @return array
 */
function url2flux_json($url) {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// Tranformation de la chaine json reçue en tableau associatif
	$json = json_decode($flux, true);

	return $json;
}

?>