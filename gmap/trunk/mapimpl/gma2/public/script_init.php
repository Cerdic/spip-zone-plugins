<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Entête des pages permettant de faire fonctionner le plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_public_script_init_dist()
{
	$out = "";
	
	// Récupérer la configuration de la version
	$key = gmap_lire_config('gmap_api_gma2', 'key', "");
	$version = gmap_lire_config('gmap_api_gma2', 'version', "2");
	
	// Inclure le script
	$google = 'http://maps.google.com/maps?file=api&v='.$version.'&key='.$key.'&hl='.$GLOBALS['spip_lang'];
	$out .= '<script type="text/javascript" src="'.$google.'"></script>'."\n";
	
	// Ajouter le markermanager (copie en local)
	$manager = find_in_path('mapimpl/gma2/javascript/markermanager.js');
	$out .= '<script type="text/javascript" src="'.$manager.'"></script>'."\n";
	
	// Ajouter les scripts spécifiques
	$local_script = find_in_path('mapimpl/gma2/javascript/gmap_impl_public.js');
	$out .= '<script type="text/javascript" src="'.$local_script.'"></script>'."\n";
	
	// CSS
	$map_styles = _DIR_PLUGIN_GMAP.'mapimpl/gma2/style/gmap_map.css';
	$out .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$map_styles.'" />'."\n";
	
	return $out;
}

?>