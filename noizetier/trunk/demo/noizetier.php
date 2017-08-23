<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/ncore_type_noisette');
include_spip('inc/ncore_noisette');
include_spip('ncore/ncore');
include_spip('ncore/noizetier');
include_spip('noizetier_fonctions');

$retour = noisette_ajouter('dashome', 'portfolio', 'content/article', 2);
var_dump($retour);

$retour = ncore_noisette_lister('dashome');
var_dump($retour);



foreach (array('dashome') as $_plugin) {
	var_dump("PLUGIN : $_plugin");

	$timestamp_debut = microtime(true);

//	$retour = ncore_noisette_lister($_plugin, 'content/article');
//	var_dump($retour);

//	$retour = noisette_ajouter($_plugin, 'copdespip', 'content/article', 1);
//	var_dump($retour);

//	$retour = ncore_noisette_lister($_plugin, 'content/article');
//	var_dump($retour);

//	$retour = noisette_ajouter($_plugin, 'main', 'content/article');
//	$retour = noisette_ajouter($_plugin, 'bloctexte', 'content/article');

//	$retour = type_noisette_charger($_plugin, 'noisettes/',  false);
//	var_dump($retour);
//	$retour = type_noisette_repertorier($_plugin, array('type' => 'article', 'composition' => ''));
//	var_dump($retour);
//	$retour = type_noisette_lire($_plugin, 'breadcrumb', 'nom');
//	var_dump($retour);
//	$retour = type_noisette_lire($_plugin, 'breadcrumb', 'nom', true);
//	var_dump($retour);

//	$retour = ncore_noisette_est_ajax($_plugin, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_est_dynamique($_plugin, 'breadcrumb');
//	var_dump($retour);
//	$retour = ncore_noisette_contexte($_plugin, 'bienvenue');
//	var_dump($retour);

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump($duree*1000);
}
