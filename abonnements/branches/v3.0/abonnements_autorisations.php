<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function abonnements_autoriser(){}


// -----------------
// Objet abonnements_offres


// bouton de menu
function autoriser_abonnementsoffres_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_abonnementsoffre_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint']; 
}

// voir les fiches completes
function autoriser_abonnementsoffre_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_abonnementsoffre_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_abonnementsoffre_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associerabonnementsoffres_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// -----------------
// Objet abonnements


// creer
function autoriser_abonnement_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint']; 
}

// voir les fiches completes
function autoriser_abonnement_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_abonnement_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// supprimer
function autoriser_abonnement_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
