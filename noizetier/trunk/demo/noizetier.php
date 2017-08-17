<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/ncore_type_noisette');
include_spip('noizetier_fonctions');

foreach (array('noizetier', 'dashome') as $_plugin) {
	var_dump("PLUGIN : $_plugin");

//	$retour = ncore_type_noisette_informer($_plugin, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_type_noisette_informer($_plugin, 'breadcrumb', '', false);
//	var_dump($retour);
//	$retour = ncore_type_noisette_informer($_plugin, 'breadcrumb', 'nom');
//	var_dump($retour);
//	$retour = ncore_type_noisette_informer($_plugin, 'breadcrumb', 'nom', true);
//	var_dump($retour);

//	$retour = ncore_noisette_est_ajax($_plugin, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_est_dynamique($_plugin, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_contexte($_plugin, 'bienvenue');
//	var_dump($retour);
}

$timestamp_debut = microtime(true);

$retour = noizetier_type_noisette_compter('article');
var_dump($retour);
$retour = noizetier_type_noisette_compter('article-evenement');
var_dump($retour);

$timestamp_fin = microtime(true);
$duree = $timestamp_fin - $timestamp_debut;
var_dump($duree*1000);
