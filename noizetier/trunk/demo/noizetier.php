<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/ncore_type_noisette');
include_spip('inc/ncore_noisette');
include_spip('ncore/ncore');
include_spip('ncore/noizetier');
include_spip('noizetier_fonctions');


foreach (array('dashboard') as $_plugin) {
	var_dump("PLUGIN : $_plugin");

	$timestamp_debut = microtime(true);

//	$retour = noisette_ajouter($_plugin, 'bloctexte', 'content/article');
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'codespip', 'content/article', 2);
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'portfolio', 'content/article');
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'main', 'content/article', 2);
//	var_dump($retour);

	noisette_deplacer($_plugin, array('squelette' => 'content/article', 'rang' => 3), 1);
//	noisette_supprimer($_plugin, array('squelette' => 'content/article', 'rang' => 3));

	$retour = ncore_noisette_lister($_plugin);
	var_dump($retour);

//	$retour = ncore_noisette_lister($_plugin, 'content/article');
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
