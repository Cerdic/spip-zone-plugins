<?php
/**
 * Plugin Spip-sondages
 * (c) 2012 Maïeul Rouquette d&#039;après Artego
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function sondages_autoriser(){}


// -----------------
// Objet sondages


// bouton de menu
function autoriser_sondages_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_sondagecreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'sondage', '', $qui, $opts);
} 

// creer
function autoriser_sondage_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND sql_countsel('spip_rubriques')>0); 
}

// voir les fiches completes
function autoriser_sondage_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_sondage_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_sondage_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// creer dans une rubrique
function autoriser_rubrique_creersondagedans_dist($faire, $type, $id, $qui, $opt) {
	return ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','sondage', $id));
}

// associer (lier / delier)
function autoriser_associersondages_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
// -----------------
// Objet choix




// creer
function autoriser_choix_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_choix_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_choix_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_choix_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// -----------------
// Objet avis


// bouton de menu
function autoriser_avis_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_avicreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'avi', '', $qui, $opts);
} 

// creer
function autoriser_avi_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_avi_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_avi_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_avi_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}




?>