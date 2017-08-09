<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('ncore_fonctions');

foreach (array('noizetier', 'dashome') as $_service) {
	var_dump("SERVICE : $_service");

	$timestamp_debut = microtime(true);

//	ncore_noisette_charger($_service, 'noisettes/', true);

//	$retour = ncore_noisette_informer($_service, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_informer($_service, 'breadcrumb', '', true);
//	var_dump($retour);
//	$retour = ncore_noisette_informer($_service, 'breadcrumb', 'nom');
//	var_dump($retour);
//	$retour = ncore_noisette_informer($_service, 'breadcrumb', 'nom', true);
//	var_dump($retour);

//	$retour = ncore_noisette_est_ajax($_service, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_est_dynamique($_service, 'breadcrumb');
//	var_dump($retour);
	$retour = ncore_noisette_contexte($_service, 'bienvenue');
	var_dump($retour);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump($duree*1000);

	$timestamp_debut = microtime(true);
	$retour = ncore_noisette_contexte($_service, 'bienvenue');
	var_dump($retour);
	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump($duree*1000);
}
