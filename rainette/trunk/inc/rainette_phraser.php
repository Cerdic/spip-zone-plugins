<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * @param string $url
 * @param string $utiliser_namespace
 *
 * @return array
 */
function url2flux_xml($url, $utiliser_namespace = 'false') {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_url($url, array('transcoder' => true));

	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'rainette');
		return array();
	}

	// Tranformation de la chaine xml reçue en tableau associatif
	$convertir = charger_fonction('simplexml_to_array', 'inc');

	// Pouvoir attraper les erreurs de simplexml_load_string() !!!
	// http://stackoverflow.com/questions/17009045/how-do-i-handle-warning-simplexmlelement-construct/17012247#17012247
	set_error_handler(function($errno, $errstr, $errfile, $errline) {
		throw new Exception($errstr, $errno);
	});

	try {
		$xml = $convertir(simplexml_load_string($flux['page']), $utiliser_namespace);
		$xml = $xml['root'];
	} catch (Exception $e) {
		restore_error_handler();
		spip_log("Erreur d'analyse XML pour l'URL `${url}` : " . $e->getMessage(), 'rainette' . _LOG_ERREUR);
		return array();
	}

	restore_error_handler();

	return $xml;
}

/**
 * @param string $url
 *
 * @return array
 */
function url2flux_json($url) {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_url($url, array('transcoder' => true));

	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'rainette');
		return array();
	}

	// Tranformation de la chaine json reçue en tableau associatif
	try {
		$json = json_decode($flux['page'], true);
	} catch (Exception $e) {
		spip_log("Erreur d'analyse JSON pour l'URL `${url}` : " . $e->getMessage(), 'rainette' . _LOG_ERREUR);
		return array();
	}

	return $json;
}
