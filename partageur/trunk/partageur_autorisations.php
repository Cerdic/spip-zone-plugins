<?php
/**
 * Plugin Partageur
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



// declaration vide pour ce pipeline.
function partageur_autoriser(){}


// -----------------
// Objet partageurs


// bouton de menu
function autoriser_partageurs_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 


// creer
function autoriser_partageur_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

// voir les fiches completes
function autoriser_partageur_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// modifier
function autoriser_partageur_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_partageur_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}




?>