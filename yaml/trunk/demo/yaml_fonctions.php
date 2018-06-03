<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ajouter_libyaml($librairies) {

	if (function_exists('yaml_parse')) {
		$librairies[] = 'libyaml';
	}

	return $librairies;
}

function decoder_fichier_yaml($filename, $options = array()) {
	$timestamp_debut = microtime(true);

	include_spip('inc/yaml');
	$file = find_in_path($filename);
	$parsed = yaml_decode_file($file, $options);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;

	return array('lib' => sinon($options['library'], 'sfyaml'), 'duree' => $duree*1000, 'yaml' => $parsed);
}

function decoder_fichier_yaml_avec_inclusions($filename, $options = array()) {
	$timestamp_debut = microtime(true);

	include_spip('inc/yaml');
	$file = find_in_path($filename);
	$parsed = yaml_charger_inclusions(yaml_decode_file($file, $options));

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;

	return array('lib' => sinon($options['library'], 'sfyaml'), 'duree' => $duree*1000, 'yaml' => $parsed);
}
