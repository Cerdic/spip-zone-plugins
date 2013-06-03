<?php
/**
 * Définit les autorisations du plugin Champs Extras (Synchronisation)
 *
 * @plugin     Champs Extras (Synchronisation)
 * @copyright  2013
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\scextras\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function scextras_autoriser()
{
}

function autoriser_scextras_synchroniser_scextras_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_scextras') => $type = 'scextras'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
}

/* Exemple
function autoriser_configurer_scextras_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_scextras') => $type = 'scextras'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/



?>