<?php
/**
 * Plugin Guestbook
 * (c) 2013 Yohann Prigent (potter64), Stephane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function guestbook_autoriser(){}


/* Exemple
function autoriser_configurer_guestbook_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_guestbook') => $type = 'guestbook'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet guestmessages


// bouton de menu
function autoriser_guestmessages_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_guestmessage_creer_dist($faire, $type, $id, $qui, $opt) {
	return true; 
}

// voir les fiches completes
function autoriser_guestmessage_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_guestmessage_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_guestmessage_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// -----------------
// Objet guestreponses


// bouton de menu
function autoriser_guestreponses_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_guestreponse_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_guestreponse_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_guestreponse_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_guestreponse_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>