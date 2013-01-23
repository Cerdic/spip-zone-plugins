<?php
/**
 * Plugin Selection d&#039;objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function selection_objet_autoriser(){}


/* Exemple
function autoriser_configurer_selection_objet_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_selection_objet') => $type = 'selection_objet'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet selection_objets




// creer
function autoriser_objet_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_objet_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_objet_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_objet_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>