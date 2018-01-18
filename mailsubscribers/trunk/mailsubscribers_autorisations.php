<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// declaration vide pour ce pipeline.
function mailsubscribers_autoriser() { }


// -----------------
// Objet mailsubscribers


// bouton de menu
function autoriser_mailsubscribers_menu_dist($faire, $type, $id, $qui, $opts) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// superinstituer : permet de passer outre les restrictions de changement de statut manuel
function autoriser_mailsubscriber_superinstituer_dist($faire, $type, $id, $qui, $opt) {
	return false;
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


// -----------------
// Objet mailsubscribinglists
// creer

function autoriser_mailsubscribinglist_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

function autoriser_mailsubscribinglist_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

function autoriser_mailsubscribinglist_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

function autoriser_mailsubscribinglist_segmenter_dist($faire, $type, $id, $qui, $opt) {
	if (!function_exists('mailsubscriber_declarer_informations_liees')) {
		include_spip('inc/mailsubscribers');
	}
	if (!test_plugin_actif('saisies')) return false;
	$declaration = mailsubscriber_declarer_informations_liees();
	if (!$declaration) return false;

	return autoriser('modifier', $type, $id, $qui, $opt);
}
