<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');
include_spip('inc/provider_caps');

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_public_script_init_dist()
{
	$out = "";
	
	// Lire la configuration
	$provider = gmap_lire_config('gmap_api_mxn', 'provider', "openlayers");
	$providerCaps = gmapmxn_getProviderCaps($provider);
	$modules = ($providerCaps['geocoder'] === 'oui') ? ",[geocoder]" : "";
	$key = gmap_lire_config('gmap_api_mxn', 'provider_key_'.$provider, "");
	
	// Insertion du script de l'api a utiliser
	if ($provider == 'cloudmade')
	{
		$out .= '<script type="text/javascript" src="http://tile.cloudmade.com/wml/latest/web-maps-lite.js"></script>'."\n";
		$out .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n".'var cloudmade_key = "'.$key.'";'."\n".'//]]>'."\n".'</script>'."\n";
	}
	if ($provider == 'google')
		$out .= '<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key='.$key.'&hl='.$GLOBALS['spip_lang'].'"></script>'."\n";
	if ($provider == 'googlev3')
		$out .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language='.$GLOBALS['spip_lang'].'"></script>'."\n";
	if ($provider == 'microsoft')
		$out .= '<script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6"></script>'."\n";
	if ($provider == 'openlayers')
		$out .= '<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js"></script>'."\n";
	if ($provider == 'ovi')
		$out .= '<script type="text/javascript" src="http://api.maps.ovi.com/jsl.js"></script>'."\n";
	if ($provider == 'yahoo')
		$out .= '<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.8&appid='.$key.'"></script>'."\n";
	if ($provider == 'yandex')
		$out .= '<script type="text/javascript" src="http://api-maps.yandex.ru/1.1/index.xml?key='.$key.'"></script>'."\n";

	// insertion de la lib mapstraction
	$lib_mxn = url_absolue(find_in_path(_DIR_LIB_MXN.'mxn.js'));
	// Apparemment ça ne marche pas avec la version minified : pour l'utiliser il faudrait probablement
	// ajouter les scripts spécifiques à chaque provider ici.
	if (strlen($lib_mxn))
		$out .= '<script id="mxn_script" type="text/javascript" src="'.$lib_mxn.'?('.$provider.$modules.')"></script>'."\n";
		
	// Patch locaux : !!!! VÉRIFIER AVEC LES NOUVELLES VERSIONS DE MAPSTRACTION !!!!
	if ($provider == 'openlayers')
	{
		$patch = _DIR_PLUGIN_GMAPMXN.'mapimpl/mxn/javascript/patch.mxn.openlayers.core.js';
		$out .= '<script type="text/javascript" src="'.$patch.'"></script>'."\n";
	}
	if ($provider == 'ovi')
	{
		$patch = _DIR_PLUGIN_GMAPMXN.'mapimpl/mxn/javascript/patch.mxn.ovi.core.js';
		$out .= '<script type="text/javascript" src="'.$patch.'"></script>'."\n";
	}
		
	// Ajouter les scripts spécifiques
	$local_script = find_in_path('mapimpl/mxn/javascript/gmap_impl_public.js');
	if (strlen($local_script))
		$out .= '<script type="text/javascript" src="'.$local_script.'"></script>'."\n";
	
	// CSS
	$map_styles = _DIR_PLUGIN_GMAPMXN.'mapimpl/mxn/style/gmap_map.css';
	if (strlen($map_styles))
		$out .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$map_styles.'" />'."\n";
	
	return $out;
}

?>