<?php
/**
 * Plugin Selection d'objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function selection_objet_autoriser(){}

// -----------------
// Objet selection_objets

// creer
function autoriser_selectionobjet_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_selectionobjet_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_selectionobjet_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

// supprimer
function autoriser_selectionobjet_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


