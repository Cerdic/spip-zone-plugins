<?php
/**
 * Définit les autorisations du plugin Accès Restreint Partiel
 *
 * @plugin     Accès Restreint Partiel
 * @copyright  2014
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\Arp\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function arp_autoriser(){}


/* Exemple
function autoriser_configurer_arp_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_arp') => $type = 'arp'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/



?>