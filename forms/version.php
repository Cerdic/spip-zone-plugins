<?php

/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

$nom = 'forms';
$version = 0.1;
define_once('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['pre_propre'] .= '|forms_avant_propre';
$GLOBALS['spip_pipeline']['spip_style'] .= '|forms_style';
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|Forms::ajouterBoutons';
$GLOBALS['spip_pipeline']['ajouter_onglets'] .= '|Forms::ajouterOnglets';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['forms_avant_propre'] = dirname(__FILE__).'/forms_filtres.php';
$GLOBALS['spip_matrice']['forms_style'] = dirname(__FILE__).'/forms_style.php';
$GLOBALS['spip_matrice']['Forms::ajouterBoutons'] =
$GLOBALS['spip_matrice']['Forms::ajouterOnglets'] =
	dirname(__FILE__).'/inc_forms.php';

include_once('forms.php');


?>