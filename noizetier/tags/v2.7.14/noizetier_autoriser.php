<?php

// S�curit�
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Fonction appel� par le pipeline
function noizetier_autoriser() {}

function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}

function autoriser_noizetier_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'noizetier', $id, $qui,  $opt);
}

/**
 * Autorisation pour configurer les noisettes d'un contenu pr�cis
 * 
 * Avoir le droit de modifier l'objet et avoir configur� cet objet pour pouvoir personnaliser ses noisettes
 **/
function autoriser_configurernoisettes_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	$liste_objets_noisettes = lire_config('noizetier/objets_noisettes', array());
	
	return
		autoriser('modifier', $type, $id)
		and in_array(table_objet_sql($type), $liste_objets_noisettes);
}
