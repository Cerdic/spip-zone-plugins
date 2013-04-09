<?php
/**
 * Plugin projets
 * (c) 2012 Cyril Marion
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function projets_autoriser(){}


/* Exemple
function autoriser_configurer_projets_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_projets') => $type = 'projets'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet projets


// bouton de menu
function autoriser_projets_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_projet_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_projet_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_projet_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_projet_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associerprojets_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
// -----------------
// Objet projets_cadres


// bouton de menu
function autoriser_projetscadres_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_projetscadre_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_projetscadre_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_projetscadre_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_projetscadre_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}



?>
