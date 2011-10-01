<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Interface de configuration de l'interface pour Google Maps v3
 *
 * Usage :
 * $init_map_defaults = charger_fonction("init_map_defaults", "mapimpl/$api/prive");
 * $init_map_defaults();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Initialisation du paramétrage pour gma3
function mapimpl_gma3_prive_init_map_defaults_dist()
{
	// Fonds de carte
	gmap_init_config('gmap_gma3_interface', 'type_carte_plan', "oui");
	gmap_init_config('gmap_gma3_interface', 'type_carte_satellite', "oui");
	gmap_init_config('gmap_gma3_interface', 'type_carte_mixte', "oui");
	gmap_init_config('gmap_gma3_interface', 'type_carte_physic', "oui");
	if (strlen(gmap_lire_config('gmap_api_gma3', 'key', "")) > 0)
		gmap_init_config('gmap_gma3_interface', 'type_carte_earth', "oui");
	else
		gmap_init_config('gmap_gma3_interface', 'type_carte_earth', "non");
	gmap_init_config('gmap_gma3_interface', 'type_defaut', "mixte");

	// Choix du type de contrôles
	gmap_init_config('gmap_gma3_interface', 'types_control_style', "menu");
	gmap_init_config('gmap_gma3_interface', 'types_control_position', "TR");
//	gmap_init_config('gmap_gma3_interface', 'nav_control_style', "large");
//	gmap_init_config('gmap_gma3_interface', 'nav_control_position', "LT");
	gmap_init_config('gmap_gma3_interface', 'zoom_control_style', "auto");
	gmap_init_config('gmap_gma3_interface', 'zoom_control_position', "LT");
	gmap_init_config('gmap_gma3_interface', 'pan_control_style', "large");
	gmap_init_config('gmap_gma3_interface', 'pan_control_position', "LT");
	gmap_init_config('gmap_gma3_interface', 'scale_control_style', "none");
	gmap_init_config('gmap_gma3_interface', 'scale_control_position', "BL");
	gmap_init_config('gmap_gma3_interface', 'streetview_control_style', "default");
	gmap_init_config('gmap_gma3_interface', 'streetview_control_position', "LT");
	gmap_init_config('gmap_gma3_interface', 'rotate_control_style', "none");
	gmap_init_config('gmap_gma3_interface', 'rotate_control_position', "LT");
	gmap_init_config('gmap_gma3_interface', 'overview_control_style', "none");
	
	// Paramètres
	gmap_init_config('gmap_gma3_interface', 'allow_dblclk_zoom', "non");
	gmap_init_config('gmap_gma3_interface', 'allow_map_dragging', "oui");
	gmap_init_config('gmap_gma3_interface', 'allow_wheel_zoom', "non");
	gmap_init_config('gmap_gma3_interface', 'allow_keyboard', "non");
}

?>
