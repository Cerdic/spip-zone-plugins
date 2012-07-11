<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function oresource_autoriser(){}


// -----------------
// Objet orr_ressources


// bouton de menu
function autoriser_orrressources_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_orrressource_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui); 
}

// voir les fiches completes
function autoriser_orrressource_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// modifier
function autoriser_orrressource_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

// supprimer
function autoriser_orrressource_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}


// -----------------
// Objet orr_reservations




// creer
function autoriser_orrreservation_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_orrreservation_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_orrreservation_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_orrreservation_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associerorrreservations_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


?>