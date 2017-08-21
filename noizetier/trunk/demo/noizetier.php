<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/ncore_type_noisette');
include_spip('inc/ncore_noisette');
include_spip('ncore/ncore');
include_spip('ncore/noizetier');
include_spip('noizetier_fonctions');

$skels = array(
	'article',
	'article-evenement',
	'content/article',
	'content/article-evenement',
	'squelettes/prive/contenu/accueil',
	'article1',
	'nav/article12',
	'prive/squelettes/contenu/article13');

foreach ($skels as $_skel) {
	$c = squelette_phraser($_skel);
	var_dump($_skel, $c);
}


foreach (array('dashome') as $_plugin) {
	var_dump("PLUGIN : $_plugin");

	$timestamp_debut = microtime(true);

//	$retour = ncore_noisette_lister($_plugin, 'content/article');
//	var_dump($retour);

//	$retour = ncore_noisette_ajouter($_plugin, 'copdespip', 'content/article', 1);
//	var_dump($retour);

//	$retour = ncore_noisette_lister($_plugin, 'content/article');
//	var_dump($retour);

//	$retour = ncore_noisette_ajouter($_plugin, 'main', 'content/article');
//	$retour = ncore_noisette_ajouter($_plugin, 'bloctexte', 'content/article');

//	$retour = ncore_type_noisette_charger($_plugin, 'noisettes/',  false);
//	var_dump($retour);
//	$retour = ncore_type_noisette_repertorier($_plugin, array('type' => 'article', 'composition' => ''));
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

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump($duree*1000);
}
