<?php
/*
 * Google Maps in SPIP plugin
 * Géolocalisation et cartographie parémétrable
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Pipelines locaux, c'est-à-dire introduits par GMap
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Pipeline des implémentations
function gmap_declare_implementation($apis)
{
	$apis['gma2'] = array( 'name' => _T('gmap:gis_api_google_maps_2'), 'explic' => _T('gmap:gis_api_google_maps_2_desc'));
	$apis['gma3'] = array( 'name' => _T('gmap:gis_api_google_maps_3'), 'explic' => _T('gmap:gis_api_google_maps_3_desc'));
	return $apis;
}

// Pipeline des outils
function _gmap_pipe_outil_geoloc($tool, $args, $nom)
{
	$formulaire = charger_fonction('outil_geoloc_'.$tool, 'formulaires', true);
	$toolDef = null;
	if ($formulaire)
	{
		$parts = $formulaire($args);
		$toolDef = array('name'=> $nom);
		foreach ($parts as $code=>$part)
			$toolDef[$code] = $part;
	}
	return $toolDef;
}
function gmap_declare_outil_geoloc($tools)
{
	if (gmap_capability('geocoder'))
		$tools['data']['geocoder'] = _gmap_pipe_outil_geoloc('geocoder', $tools['args'], _T('gmap:outil_geocoder_nom'));
	
	$objet = $tools['args']['objet'];
	if (($objet === 'article') || ($objet === 'document'))
		$tools['data']['siblings'] = _gmap_pipe_outil_geoloc('siblings', $tools['args'], _T('gmap:outil_siblings_nom'));
		
	return $tools;
}


?>