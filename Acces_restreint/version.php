<?php

/*
 * Gestion des Acces restreint par rubriques
 * 
 *
 * Auteur :
 * par cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'Acces Restreint';
$version = 0.1;
define('_DIR_PLUGIN_ACCES_RESTREINT',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|AccesRestreint::ajouterBoutons';
$GLOBALS['spip_pipeline']['ajouter_onglets'] .= '|AccesRestreint::ajouterOnglets';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['AccesRestreint::ajouterBoutons'] =
$GLOBALS['spip_matrice']['AccesRestreint::ajouterOnglets'] =
	dirname(__FILE__).'/inc_acces_restreint.php';

include_once('inc_acces_restreint_base.php');
include_once('acces_restreint.php');


?>