<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
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

// Initialisation du param�trage pour gma3
function mapimpl_gma3_prive_init_map_defaults_dist($profile = 'interface')
{
	// Clefs d'acc�s aux valeurs enregistr�es
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_gma3_'.$profile;
	
	// Fonds de carte
	gmap_init_config($apiConfigKey, 'type_carte_plan', "oui");
	gmap_init_config($apiConfigKey, 'type_carte_satellite', "oui");
	gmap_init_config($apiConfigKey, 'type_carte_mixte', "oui");
	gmap_init_config($apiConfigKey, 'type_carte_physic', "oui");
	if (strlen(gmap_lire_config('gmap_api_gma3', 'key', "")) > 0)
		gmap_init_config($apiConfigKey, 'type_carte_earth', "oui");
	else
		gmap_init_config($apiConfigKey, 'type_carte_earth', "non");
	gmap_init_config($apiConfigKey, 'type_defaut', "mixte");

	// Choix du type de contr�les
	gmap_init_config($apiConfigKey, 'types_control_style', "menu");
	gmap_init_config($apiConfigKey, 'types_control_position', "TR");
//	gmap_init_config($apiConfigKey, 'nav_control_style', "large");
//	gmap_init_config($apiConfigKey, 'nav_control_position', "LT");
	gmap_init_config($apiConfigKey, 'zoom_control_style', "auto");
	gmap_init_config($apiConfigKey, 'zoom_control_position', "LT");
	gmap_init_config($apiConfigKey, 'pan_control_style', "large");
	gmap_init_config($apiConfigKey, 'pan_control_position', "LT");
	gmap_init_config($apiConfigKey, 'scale_control_style', "none");
	gmap_init_config($apiConfigKey, 'scale_control_position', "BL");
	gmap_init_config($apiConfigKey, 'streetview_control_style', "default");
	gmap_init_config($apiConfigKey, 'streetview_control_position', "LT");
	gmap_init_config($apiConfigKey, 'rotate_control_style', "none");
	gmap_init_config($apiConfigKey, 'rotate_control_position', "LT");
	gmap_init_config($apiConfigKey, 'overview_control_style', "none");
	
	// Param�tres
	gmap_init_config($apiConfigKey, 'allow_dblclk_zoom', "non");
	gmap_init_config($apiConfigKey, 'allow_map_dragging', "oui");
	gmap_init_config($apiConfigKey, 'allow_wheel_zoom', "non");
	gmap_init_config($apiConfigKey, 'allow_keyboard', "non");
}

?>
