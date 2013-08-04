<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Pipelines
*
**/

include_spip ('public/assembler');

function geoportail_insert_head($flux)
{	// Logo des couches SPIP
	$logo = find_in_path(_DIR_IMG."geoserviceon0.png");
	if (!$logo) $logo = find_in_path(_DIR_IMG."geoserviceon0.gif");
	if (!$logo) $logo = find_in_path(_DIR_IMG."geoserviceon0.jpg");
	if ($logo) $logo = "jQuery.geoportail.setOriginator ('$logo','".$GLOBALS['meta']['adresse_site']."')";
	// Type de popup
	$popup = $GLOBALS['meta']['geoportail_popup'];
	$popup = "jQuery.geoportail.spip_popup = '".($popup?$popup:"Anchored")."';"
		." jQuery.geoportail.hover = ".($GLOBALS['meta']['geoportail_hover'] ?"true;":"false;");
	// Repertoire du popup
	$repop = dirname(find_in_path('img/cloud-popup-relative.png'));
	$repop = "jQuery.geoportail.imgPath = '$repop/';";
	// Zoom client
	$zclient = "spipGeoportail.zoomClient = ".($GLOBALS['meta']['geoportail_zclient']?'true':'false').";";

	$flux .=
'
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/geoportail.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.dialog.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/geoprofil.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.cookie.js"></script>
<script type="text/javascript">
'
.($GLOBALS['meta']['geoportail_bing_key'] ? "\njQuery.geoportail.bingKey='".$GLOBALS['meta']['geoportail_bing_key']."';" : "")
.($GLOBALS['meta']['geoportail_osm_tah'] ? "jQuery.geoportail.osm_tah=true;" : "jQuery.geoportail.osm_tah=false;")
."\n"
.($GLOBALS['meta']['geoportail_osm_mquest'] ? "jQuery.geoportail.osm_mquest=true;" : "jQuery.geoportail.osm_mquest=false;")
."\n"
.($GLOBALS['meta']['geoportail_osm_layer'] ? "jQuery.geoportail.osm_layer='".$GLOBALS['meta']['geoportail_osm_layer']."';" : "jQuery.geoportail.osm_mquest='mapnik';")
."\n".$logo
."\n".$popup
."\n".$repop
."\n".$zclient
.'
</script>';
$flux .= recuperer_fond('geoportail_insert_head', array('key'=>$GLOBALS['meta']['geoportail_key']) );
return $flux;
}


function geoportail_header_prive($flux) {
$flux .= recuperer_fond('geoportail_header_prive');
return $flux;
}


?>