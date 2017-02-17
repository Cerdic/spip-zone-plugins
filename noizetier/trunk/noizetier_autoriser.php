<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Fonction appelée par le pipeline
function noizetier_autoriser() {}

function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}

function autoriser_noizetier_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'noizetier', $id, $qui,  $opt);
}

/**
 * Autorisation de voir la configuration des noisettes d'une page ou d'un objet.
 * 
 **/
function autoriser_voirnoizetier_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation pour configurer les noisettes d'un contenu précis
 *
 * Avoir le droit de modifier l'objet et avoir configuré cet objet pour pouvoir personnaliser ses noisettes
 **/
function autoriser_configurernoisettes_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	$liste_objets_noisettes = lire_config('noizetier/objets_noisettes', array());

	return
		autoriser('modifier', $type, $id)
		and in_array(table_objet_sql($type), $liste_objets_noisettes);
}
