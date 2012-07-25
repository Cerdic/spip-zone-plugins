<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function jeux_autoriser(){}


// -----------------
// Objet jeux


// bouton de menu
function autoriser_jeux_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_jeux_creer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'jeu', '', $qui, $opts);
} 

// creer
function autoriser_jeu_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite'))); 
}

// voir les fiches completes
function autoriser_jeu_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_jeu_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_jeu_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

?>