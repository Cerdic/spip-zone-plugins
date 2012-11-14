<?php
/**
 * Plugin Annonces services
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function ad_service_autoriser(){}


// -----------------
// Objet ad_services


// bouton de menu
function autoriser_adservices_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_adservicecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'ad_service', '', $qui, $opts);
} 

// creer
function autoriser_adservice_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_adservice_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_adservice_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_adservice_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>