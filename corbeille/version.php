<?php

/*
 * forms
 * version plug-in de corbeille
 *
 * Auteur :
 * Copyright (C) 2002 ONFRAY MATTHIEU
 * mail : silicium@japanim.net
 * web amateur : http://www.japanim.net/
 * web technique : http://spip.japanim.net/
 * adaptation en plugin par cedric.morin@yterium.com
 * © 2005-2006 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'corbeille';
$version = 0.1;
define_once('_DIR_PLUGIN_CORBEILLE',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|Corbeille::ajouterBoutons';
$GLOBALS['spip_pipeline']['ajouter_onglets'] .= '|Corbeille::ajouterOnglets';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['Corbeille::ajouterBoutons'] =
$GLOBALS['spip_matrice']['Corbeille::ajouterOnglets'] =
	dirname(__FILE__).'/inc_corbeille.php';

?>