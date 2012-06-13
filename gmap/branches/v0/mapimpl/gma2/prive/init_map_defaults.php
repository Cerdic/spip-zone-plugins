<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
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

// Initialisation du param�trage pour gma2
function mapimpl_gma2_prive_init_map_defaults_dist($profile = 'interface')
{
	// Clefs d'acc�s aux valeurs enregistr�es
	if (!isset($profile))
		$profile = 'interface';
	$apiConfigKey = 'gmap_gma2_'.$profile;
	
	// Fonds de carte
	gmap_init_config($apiConfigKey, 'type_carte_plan', 'oui');
	gmap_init_config($apiConfigKey, 'type_carte_satellite', 'oui');
	gmap_init_config($apiConfigKey, 'type_carte_mixte', 'oui');
	gmap_init_config($apiConfigKey, 'type_carte_physic', 'oui');
	gmap_init_config($apiConfigKey, 'type_carte_earth', 'non');
	gmap_init_config($apiConfigKey, 'type_defaut', 'mixte');

	// Choix du type de contr�les
	gmap_init_config($apiConfigKey, 'types_control_style', 'menu');
	gmap_init_config($apiConfigKey, 'nav_control_style', '3D');
	
	// Param�tres
	gmap_init_config($apiConfigKey, 'allow_dblclk_zoom', 'non');
	gmap_init_config($apiConfigKey, 'allow_continuous_zoom', 'non');
	gmap_init_config($apiConfigKey, 'allow_wheel_zoom', 'non');
}

?>
