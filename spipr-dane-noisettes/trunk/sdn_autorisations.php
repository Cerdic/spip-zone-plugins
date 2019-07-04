<?php
/**
 * Définit les autorisations du plugin SPIPr-Dane-Noisettes
 *
 * @plugin     SPIPr-Dane-Noisettes
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdn\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function sdn_autoriser() {
}


/* Exemple
function autoriser_sdn_configurer_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_sdn') => $type = 'sdn'
	// au choix :
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/
