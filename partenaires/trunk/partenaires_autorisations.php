<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function partenaires_autoriser(){}


// -----------------
// Objet partenaires


// bouton de menu
function autoriser_partenaires_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_partenairecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'partenaire', '', $qui, $opts);
} 

// creer
function autoriser_partenaire_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// voir les fiches completes
function autoriser_partenaire_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_partenaire_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_partenaire_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// -----------------
// Objet types


// bouton de menu
function autoriser_types_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_typecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'type', '', $qui, $opts);
} 

// creer
function autoriser_type_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// voir les fiches completes
function autoriser_type_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_type_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_type_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associertypes_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


?>