<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function mailsuscribers_autoriser(){}


// -----------------
// Objet mailsuscribers


// bouton de menu
function autoriser_mailsuscribers_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_mailsuscriber_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// iconifier
function autoriser_mailsuscriber_iconifier_dist($faire, $type, $id, $qui, $opt) {
	return false; // pas de logo
}

// voir les fiches completes
function autoriser_mailsuscriber_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_mailsuscriber_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_mailsuscriber_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>