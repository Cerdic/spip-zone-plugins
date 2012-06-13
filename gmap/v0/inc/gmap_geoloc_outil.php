<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Fonctions utiles pour gérer les requêtes ajax qui renvoient des données json
 *
 */
 
include_spip('inc/utils');

define('_DOCTYPE_JSON',	"");
define('_DOCTYPE_KML',	"");

// Page GeoJSON
function _gmap_http_no_cache_geojson()
{
	if (headers_sent())
		{ spip_log("http_no_cache arrive trop tard"); return; }
		
	header("Content-Type: text/plain; charset=utf-8");
	header("Expires: 0");
	header("Last-Modified: " .gmdate("D, d M Y H:i:s"). " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
}
function gmap_commencer_page_geojson()
{
	_gmap_http_no_cache_geojson();
	return _DOCTYPE_JSON.'{	"type": "FeatureCollection", "features": ['. "\n";
}
function gmap_fin_page_geojson()
{
	return '
	]
}';
}

// Ajout d'un point en json
function _gmap_propriete_geojson($point, $champ, $premier = false)
{
	if (!isset($champ) || !isset($point) ||!isset($point[$champ]))
		return '';
	$out = '';
	$out .= $point[$champ];
	if (is_string($point[$champ]));
		$out = texte_json($out);
	return (!$premier ? ',' : '').'
	"'.$champ.'": '.$out;
}
function gmap_ajoute_point_geojson($point,$premier)
{
	$props = '';
	if (isset($point['html']))
		$point['html'] = html_body($point['html']);
	
	foreach ($point as $key => $value)
	{
		$prop = $value;
		if (is_string($value))
			$prop = texte_json($prop);
		$props .= (strlen($props) ? ',' : '').'
		"'.$key.'": '.$prop;
	}
	
	return (!$premier ? ',
' : '').'{"type": "Feature",
	"geometry": {"type": "Point", "coordinates": ['.$point['longitude'].','.$point['latitude'].']},
	"properties": {'.$props.'}}';
}

?>