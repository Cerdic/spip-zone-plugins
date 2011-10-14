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
function mapimpl_gma3_public_script_init_dist()
{
	$out = "";
	
	// Récupérer la configuration de la version
	$key = gmap_lire_config('gmap_api_gma3', 'key', "");
	$version = gmap_lire_config('gmap_api_gma3', 'version', "3");
	$isEarth = (gmap_lire_config('gmap_gma3_interface', 'type_carte_earth', "non") === 'oui') ? true : false;
	
	// Inclure le script
	$google = 'http://maps.google.com/maps/api/js?sensor=false&v='.$version.'&language='.$GLOBALS['spip_lang'];
	$out .= '<script type="text/javascript" src="'.$google.'"></script>'."\n";
	
	// Google load & Google Earth, seulement si on a une clef et que earth est demandé
	if ($isEarth && (strlen($key) > 0))
	{
		// Google API loader
		$gload = 'https://www.google.com/jsapi?key='.$key;
		$out .= '<script type="text/javascript" src="'.$gload.'"></script>'."\n";
		
		// Chargemeent de Google Earth
		$out .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
		$out .= 'google.load("earth", "1");'."\n";
		$out .= '//]]>'."\n".'</script>'."\n";
	
		// Plugin google earth (http://google-maps-utility-library-v3.googlecode.com/svn/trunk/googleearth/)
		$earth = find_in_path('mapimpl/gma3/javascript/googleearth.js');
		$out .= '<script type="text/javascript" src="'.$earth.'"></script>'."\n";
	}
	
	// Ajouter les scripts spécifiques
	$local_script = find_in_path('mapimpl/gma3/javascript/gmap_impl_public.js');
	$out .= '<script type="text/javascript" src="'.$local_script.'"></script>'."\n";
	
	// CSS
	$map_styles = _DIR_PLUGIN_GMAP.'mapimpl/gma3/style/gmap_map.css';
	$out .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$map_styles.'" />'."\n";
	
	return $out;
}

?>