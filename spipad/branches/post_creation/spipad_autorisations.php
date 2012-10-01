<?php
/**
 * Plugin Annonces
 * (c) 2012 apéro spip
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function spipad_autoriser(){}


/* Exemple
function autoriser_configurer_spipad_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_spipad') => $type = 'spipad'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/

// -----------------
// Objet ads


// bouton de menu
function autoriser_ads_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_adcreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'ad', '', $qui, $opts);
} 

// creer
function autoriser_ad_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND sql_countsel('spip_rubriques')>0); 
}

// voir les fiches completes
function autoriser_ad_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_ad_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_ad_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// creer dans une rubrique
function autoriser_rubrique_creeraddans_dist($faire, $type, $id, $qui, $opt) {
	return ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','ad', $id));
}

// associer (lier / delier)
function autoriser_associerads_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


?>