<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function verifier_libyaml() {
	if (function_exists('yaml_parse')) {
		$message = 'libYAML dispo';
	} else {
		$message = 'libYAML indispo';
	}

	return $message;
}

function libyaml_parse_file($file) {
	$timestamp_debut = microtime(true);

	$parsed = yaml_parse_file($file, 0);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('libyaml : ', $duree*1000);

	return $parsed;
}

function sfyaml_parse_file($file) {
	$timestamp_debut = microtime(true);

	include_spip('inc/flock');
	lire_fichier($file, $yaml);
	// Si on recupere bien quelque chose
	if ($yaml) {
		include_spip('inc/yaml_sfyaml');
		$parsed = yaml_sfyaml_decode($yaml);
	}

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('sfyaml : ', $duree*1000);

	return $parsed;
}

function symfony_parse_file($file) {
	$timestamp_debut = microtime(true);

	include_spip('inc/yaml_symfony');
	$parsed = yaml_symfony_decode_file($file);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('symfony : ', $duree*1000);

	return $parsed;
}


function libyaml_parse($file) {
	$timestamp_debut = microtime(true);

	include_spip('inc/flock');
	lire_fichier($file, $input);

	$parsed = yaml_parse($input, 0);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('libyaml : ', $duree*1000);

	return $parsed;
}

function sfyaml_parse($file) {
	$timestamp_debut = microtime(true);

	include_spip('inc/flock');
	lire_fichier($file, $input);

	// Si on recupere bien quelque chose
	include_spip('inc/yaml_sfyaml');
	$parsed = yaml_sfyaml_decode($input);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('sfyaml : ', $duree*1000);

	return $parsed;
}

function symfony_parse($file) {
	$timestamp_debut = microtime(true);

	include_spip('inc/flock');
	lire_fichier($file, $input);

	include_spip('inc/yaml_symfony');
	$parsed = yaml_symfony_decode($input);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump('symfony : ', $duree*1000);

	return $parsed;
}

