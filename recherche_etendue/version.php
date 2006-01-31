<?php

/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * fil pour le panneau admin_index d'origine
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */


$nom = 'Recherche Etendue';
$version = 0.1;
define_once('_DIR_PLUGIN_ADVANCED_SEARCH',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|RechercheEtendue::ajouterBoutons';
$GLOBALS['spip_pipeline']['ajouter_onglets'] .= '|RechercheEtendue::ajouterOnglets';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['RechercheEtendue::ajouterBoutons'] =
$GLOBALS['spip_matrice']['RechercheEtendue::ajouterOnglets'] =
	dirname(__FILE__).'/recherche_etendue.php';

include_once('boucle_indexation.php'); // la plomberie pour la boucle INDEX

include_once('recherche_etendue.php'); // les filtres de presentation de la recherche

?>