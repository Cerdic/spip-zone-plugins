<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function mailsubscribers_autoriser(){}


// -----------------
// Objet mailsubscribers


// bouton de menu
function autoriser_mailsubscribers_menu_dist($faire, $type, $id, $qui, $opts){
        return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
} 


// creer
function autoriser_mailsubscriber_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// iconifier
function autoriser_mailsubscriber_iconifier_dist($faire, $type, $id, $qui, $opt) {
	return false; // pas de logo
}

// voir les fiches completes
function autoriser_mailsubscriber_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// modifier
function autoriser_mailsubscriber_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_mailsubscriber_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>
