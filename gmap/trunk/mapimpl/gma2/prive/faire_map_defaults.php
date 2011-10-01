<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'interface pour Google Maps v2
 *
 * Usage :
 * $faire_map_defaults = charger_fonction("faire_map_defaults", "mapimpl/$api/prive");
 * $faire_map_defaults();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_prive_faire_map_defaults_dist()
{
	// Fonds de carte
	gmap_ecrire_config('gmap_gma2_interface', 'type_carte_plan', ((_request('type_carte_plan') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'type_carte_satellite', ((_request('type_carte_satellite') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'type_carte_mixte', ((_request('type_carte_mixte') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'type_carte_physic', ((_request('type_carte_physic') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'type_carte_earth', ((_request('type_carte_earth') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'type_defaut', _request('type_carte_defaut'));

	// Choix du type de contrôles
	gmap_ecrire_config('gmap_gma2_interface', 'types_control_style', _request('types_control_style'));
	gmap_ecrire_config('gmap_gma2_interface', 'nav_control_style', _request('nav_control_style'));
	
	// Paramètres
	gmap_ecrire_config('gmap_gma2_interface', 'allow_dblclk_zoom', ((_request('allow_dblclk_zoom') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'allow_continuous_zoom', ((_request('allow_continuous_zoom') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_gma2_interface', 'allow_wheel_zoom', ((_request('allow_wheel_zoom') === "oui") ? "oui" : "non"));
	
	return "";
}

?>
