<?php
/**
 * Définit les autorisations du plugin Champs Extras (Synchronisation)
 *
 * @plugin     Champs Extras (Synchronisation)
 * @copyright  2013
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\Sextras\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function sextras_autoriser()
{
}

function autoriser_sextras_synchroniser_sextras_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_sextras') => $type = 'sextras'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
}

/* Exemple
function autoriser_configurer_sextras_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_sextras') => $type = 'sextras'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/



?>