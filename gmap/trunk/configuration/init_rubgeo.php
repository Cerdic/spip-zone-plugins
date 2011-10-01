<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Initialisation de paramètres sur les rubriques et objet autorisé
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_init_rubgeo_dist()
{
	// Autoriser sur tous les objets
	gmap_init_config('gmap_objets_geo', 'type_rubriques', "oui");
	gmap_init_config('gmap_objets_geo', 'type_articles', "oui");
	gmap_init_config('gmap_objets_geo', 'type_documents', "oui");
	gmap_init_config('gmap_objets_geo', 'type_breves', "oui");
	gmap_init_config('gmap_objets_geo', 'type_mots', "oui");
	gmap_init_config('gmap_objets_geo', 'type_auteurs', "oui");

	// Tout le site et liste des rubriques vide
	gmap_init_config('gmap_objets_geo', 'tout_le_site', "oui");
	gmap_init_config('gmap_objets_geo', 'liste', "");
}

?>
