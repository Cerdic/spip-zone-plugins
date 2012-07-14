<?php
/**
 * Plugin kaye
 * (c) 2012 Cédric Couvrat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function kaye_autoriser(){}


/* Exemple
function autoriser_configurer_kaye_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_kaye') => $type = 'kaye'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/


// -----------------
// Objet classes


// bouton de menu
function autoriser_classes_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_classe_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui); 
}

// voir les fiches completes
function autoriser_classe_voir_dist($faire, $type, $id, $qui, $opt) {
	 //return ($id == $qui['id_auteur']);
	return true;
}

// modifier
function autoriser_classe_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_classe_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}



// -----------------
// Objet devoirs


// bouton de menu
function autoriser_devoirs_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_devoircreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'devoir', '', $qui, $opts);
} 

// creer
function autoriser_devoir_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_devoir_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_devoir_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_devoir_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}




?>