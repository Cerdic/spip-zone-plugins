<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * @param string $url
 * @param string $utiliser_namespace
 * @return array
 */
function url2flux_xml($url, $utiliser_namespace='false') {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	$convertir = charger_fonction('simplexml_to_array', 'inc');
	$xml = $convertir(simplexml_load_string($flux), $utiliser_namespace);
	$xml = $xml['root'];

	return $xml;
}

/**
 * @param string $url
 * @return mixed
 */
function url2flux_json($url) {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// On tranforme la chaine json en tableau associatif
	$json = json_decode($flux, true);

	return $json;
}

?>