<?php
/**
 * Plugin Chatbox
 * (c) 2013 g0uZ
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function chatbox_autoriser(){}


/* Exemple
function autoriser_configurer_chatbox_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_chatbox') => $type = 'chatbox'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet chatbox_messages


// bouton de menu
function autoriser_chatboxmessages_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_chatboxmessagecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return false;
} 

// creer
function autoriser_chatboxmessage_creer_dist($faire, $type, $id, $qui, $opt) {
	return true; 
}

// voir les fiches completes
function autoriser_chatboxmessage_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_chatboxmessage_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_chatboxmessage_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}




?>