<?php

/*
 * gestion_documents
 *
 * Dump et restore XML complets
 * des tables principales et tables auxiliaures
 * et restore des dumps xml phpMyAdmin
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */

$nom = 'Super Dump';
$version = 0.1;

define_once('_DIR_PLUGIN_SUPER_DUMP',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|SuperDump::ajouterBoutons';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['SuperDump::ajouterBoutons'] =
	dirname(__FILE__).'/super_dump.php';

include_once('super_dump.php');

?>