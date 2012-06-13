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
 * $init_api = charger_fonction("init_api", "mapimpl/$api/prive");
 * $init_api();
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Initialisation du paramétrage pour gma2
function mapimpl_gma2_prive_init_api_dist()
{
	gmap_init_config('gmap_api_gma2', 'key', '');
	gmap_init_config('gmap_api_gma2', 'version', '2.s');
}

?>
