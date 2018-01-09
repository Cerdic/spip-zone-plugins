<?php
/**
 * Plugin Déclinaisons Prix
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

// declaration vide pour ce pipeline.
function declinaisons_autoriser() {
}

// -----------------
// Objet declinaisons

// bouton de menu
function autoriser_declinaisons_menu_dist($faire, $type, $id, $qui, $opts) {
	return true;
}

// bouton d'outils rapides
function autoriser_declinaisoncreer_menu_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('creer', 'declinaison', '', $qui, $opts);
}

// creer
function autoriser_declinaison_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite'
	));
}

// voir les fiches completes
function autoriser_declinaison_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_declinaison_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite'
	));
}

// supprimer
function autoriser_declinaison_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}
