<?php
/**
 * Plugin SpipAd - 2roues
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function ad_deux_roues_autoriser(){}


// -----------------
// Objet ad_deux_roues


// bouton de menu
function autoriser_addeuxroues_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_addeuxrouecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'ad_deux_roue', '', $qui, $opts);
} 

// creer
function autoriser_addeuxroue_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_addeuxroue_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_addeuxroue_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_addeuxroue_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>