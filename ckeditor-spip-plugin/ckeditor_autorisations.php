<?php
/**
 * Plugin ckeditor-spip3-plugin
 * (c) 2012 Frédéric Bonnaud
 * Licence GNU/GPL v2
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function ckeditor_autoriser(){}


/* Exemple
function autoriser_configurer_ckeditor_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_ckeditor') => $type = 'ckeditor'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/



?>