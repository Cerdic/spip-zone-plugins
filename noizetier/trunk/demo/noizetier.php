<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/ncore_type_noisette');
include_spip('inc/ncore_noisette');
include_spip('ncore/ncore');
include_spip('ncore/noizetier');

$contexte = array('objet' => 'auteur', 'id' => 12);

foreach (array('noiZetier') as $_plugin) {
	var_dump("PLUGIN : $_plugin");

	$timestamp_debut = microtime(true);

//	$conteneur = array('squelette' => 'content/article');
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'rang');
//	var_dump('conteneur tout info rang', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'id_noisette');
//	var_dump('conteneur tout info id_noisette', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'rang');
//	var_dump('conteneur info noisette rang', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'id_noisette');
//	var_dump('conteneur info noisette id_noisette', $retour);

//	$conteneur = array('squelette' => 'content', 'objet' => 'article', 'id_objet' => 11);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'rang');
//	var_dump('conteneur tout info rang', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'id_noisette');
//	var_dump('conteneur tout info id_noisette', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'rang');
//	var_dump('conteneur info noisette rang', $retour);
//	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'id_noisette');
//	var_dump('conteneur info noisette id_noisette', $retour);

	$conteneur = array();
	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'rang');
	var_dump('conteneur tout info rang', $retour);
	$retour = noizetier_noisette_lister($_plugin, $conteneur, '', $cle = 'id_noisette');
	var_dump('conteneur tout info id_noisette', $retour);
	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'rang');
	var_dump('conteneur info noisette rang', $retour);
	$retour = noizetier_noisette_lister($_plugin, $conteneur, 'noisette', $cle = 'id_noisette');
	var_dump('conteneur info noisette id_noisette', $retour);


//	$retour = noisette_ajouter($_plugin, 'bloctexte', 'content/article', array());
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'codespip', 'content/article', array(), 2);
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'portfolio', 'content/article', array());
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'main', 'content/article', array(), 2);
//	var_dump($retour);

//	$retour = noisette_ajouter($_plugin, 'bloctexte', 'content/article', $contexte);
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'codespip', 'content/article', $contexte, 2);
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'portfolio', 'content/article', $contexte);
//	var_dump($retour);
//	$retour = noisette_ajouter($_plugin, 'main', 'content/article', $contexte);
//	var_dump($retour);

//	noisette_supprimer($_plugin, array('squelette' => 'content/article', 'contexte' => array(), 'rang' => 4));
//	noisette_supprimer($_plugin, array('squelette' => 'content/article', 'contexte' => $contexte, 'rang' => 2));

//	noisette_deplacer($_plugin, array('squelette' => 'content/article', 'contexte' => array(), 'rang' => 3), 1);
//	noisette_deplacer($_plugin, array('squelette' => 'content/article', 'contexte' => $contexte, 'rang' => 3), 1);

//	$retour = noisette_lire($_plugin, array('squelette' => 'content/article', 'contexte' => array(), 'rang' => 3), '');
//	var_dump($retour);
//	$retour = noisette_lire($_plugin, array('squelette' => 'content/article', 'contexte' => array(), 'rang' => 3), 'id_noisette');
//	var_dump($retour);

//	$retour = noisette_lire($_plugin, array('squelette' => 'content/article', 'contexte' => $contexte, 'rang' => 3), '');
//	var_dump($retour);
//	$retour = noisette_lire($_plugin, array('squelette' => 'content/article', 'contexte' => $contexte, 'rang' => 3), 'id_noisette');
//	var_dump($retour);

//	noisette_vider($_plugin, 'content/article', array());
//	noisette_vider($_plugin, 'content/article', $contexte);

//	var_dump(lire_config('dashboard_noisettes'));

	$timestamp_fin = microtime(true);
	$duree = $timestamp_fin - $timestamp_debut;
	var_dump($duree*1000);
}
