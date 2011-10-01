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
 * $init_map_defaults = charger_fonction("init_map_defaults", "mapimpl/$api/prive");
 * $init_map_defaults();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Initialisation du paramétrage pour gma2
function mapimpl_gma2_prive_init_map_defaults_dist()
{
	// Fonds de carte
	gmap_init_config('gmap_gma2_interface', 'type_carte_plan', 'oui');
	gmap_init_config('gmap_gma2_interface', 'type_carte_satellite', 'oui');
	gmap_init_config('gmap_gma2_interface', 'type_carte_mixte', 'oui');
	gmap_init_config('gmap_gma2_interface', 'type_carte_physic', 'oui');
	gmap_init_config('gmap_gma2_interface', 'type_carte_earth', 'non');
	gmap_init_config('gmap_gma2_interface', 'type_defaut', 'mixte');

	// Choix du type de contrôles
	gmap_init_config('gmap_gma2_interface', 'types_control_style', 'menu');
	gmap_init_config('gmap_gma2_interface', 'nav_control_style', '3D');
	
	// Paramètres
	gmap_init_config('gmap_gma2_interface', 'allow_dblclk_zoom', 'non');
	gmap_init_config('gmap_gma2_interface', 'allow_continuous_zoom', 'non');
	gmap_init_config('gmap_gma2_interface', 'allow_wheel_zoom', 'non');
}

?>
