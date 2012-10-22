<?php
/**
 * Plugin Feuille
 * (c) 2012 chankalan
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function feuille_autoriser(){}


// -----------------
// Objet feuilles


// bouton de menu
function autoriser_feuilles_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_feuille_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_feuille_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_feuille_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_feuille_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}




?>