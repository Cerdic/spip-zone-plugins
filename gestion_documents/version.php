<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */

$nom = 'gestion_documents';
$version = 0.1;

define_once('_DIR_PLUGIN_GESTION_DOCUMENTS',(_DIR_PLUGINS . basename(dirname(__FILE__))));

// s'inserer dans les pipelines
$GLOBALS['spip_pipeline']['ajouter_boutons'] .= '|GestionDocuments::ajouterBoutons';

// la matrice des fonctions pipeline
$GLOBALS['spip_matrice']['GestionDocuments::ajouterBoutons'] =
	dirname(__FILE__).'/gestion_documents.php';

include_once('gestion_documents.php');

?>