<?php

/*
 * mots partout
 *
 * interface de gestion des mots clefs
 *
 * Auteur : Pierre Andrews (Mortimer)
 * © 2006 - Distribue sous licence GPL
 *
 */

$nom = 'mots_partout';
$version = 0.1;

define_once('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|MotsPartout::ajouterBoutons';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['MotsPartout::ajouterBoutons'] =
	dirname(__FILE__).'/mots_partout.php';


?>
