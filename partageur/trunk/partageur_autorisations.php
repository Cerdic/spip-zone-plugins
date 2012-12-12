<?php
/**
 * Plugin Partageur
 * (c) 2012 erational
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function partageur_autoriser(){}


/* Exemple
function autoriser_configurer_partageur_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_partageur') => $type = 'partageur'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet partageurs


// bouton de menu
function autoriser_partageurs_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_partageur_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// voir les fiches completes
function autoriser_partageur_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// modifier
function autoriser_partageur_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_partageur_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}




?>