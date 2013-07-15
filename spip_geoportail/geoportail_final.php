<?php

/** Recalcule sur chaque page (hors cache) 
*/
function geoportail_affichage_final($page)
{	// on regarde rapidement si la page inclus un geoportail
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_')===FALSE) return $page;

	// Inclure le hash code de maniere dynamique
	charger_fonction('securiser_action','inc');
	$action = calculer_action_auteur('geoportail');
	
	// Version de l'API
	$version = '2.0.3';
	// if ($GLOBALS['meta']['geoportail_gpp3']) $version = '2.0.0beta';
	// Cle GPP
	$gppkey = $GLOBALS['meta']['geoportail_key'];

	// Version debug de l'API
	if ($GLOBALS['geoportail_debug']) $api = "http://depot.ign.fr/geoportail/api/js/$version/lib/geoportal/lib/Geoportal.js";
	// Version locale de l'API
	else if ($GLOBALS['meta']['geoportail_js']) $api = find_in_path ("js/GeoportalExtended.js");
	// ...ou sur le site de l'API
	else $api = "http://api.ign.fr/geoportail/api/js/$version/GeoportalExtended.js";
	
	$api =
'	
<!-- API Geoportail -->
<script type="text/javascript" src="'.$api.'" charset="UTF-8">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Layer/Locator.js">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Format/Ceoconcept_rip.js">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Layer/GXT.js">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Popup/SpipPopup.js">// <![CDATA[
    // ]]></script>
';
	
	$header =
'<script type="text/javascript">
spipGeoportail.hash = "'.$action.'";
spipGeoportail.versionAPI = "'.$version.'";</script>
<script type="text/javascript">jQuery(document).ready(	function() { spipGeoportail.loadAPI("'.$gppkey.'"); });</script>

<!-- OpenLayers styles : -->
<link id="__OpenLayersCss__" rel="stylesheet" type="text/css" href="http://api.ign.fr/geoportail/api/js/'.$version.'/theme/default/style.css"/>
<link id="__FramedCloudOpenLayersCss__" rel="stylesheet" type="text/css" href="http://api.ign.fr/geoportail/api/js/'.$version.'/theme/default/framedCloud.css"/>
<link id="__GeoportalCss__" rel="stylesheet" type="text/css" href="http://api.ign.fr/geoportail/api/js/'.$version.'/theme/geoportal/style.css"/>
';
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_YHOO-->'))
	{	$ykey = $GLOBALS['meta']['geoportail_yahoo_key'];
		if ($ykey) $api .= '<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid='.($ykey?$ykey:'TEST').'"></script>';
		else $api .= '<script type="text/javascript">alert ("NO Yahoo Map key defined")</script>';
	}
	/*
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_BING-->'))
	{	// $header .= '<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>';
	}
	*/
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_GMAP-->'))
	{	$api .= '<script src="http://maps.google.com/maps/api/js?v=3&sensor=false"></script>';
		$header .= '<link id="__GoogleOpenLayersCss__" rel="stylesheet" type="text/css" href="http://api.ign.fr/geoportail/api/js/'.$version.'/theme/default/google.css"/>';
	}
		
	// Inclure l'API dans le Header
	$page = preg_replace('/<!--_GEOPORTAIL_HEADER_-->/', $header, $page, 1);
	return str_replace ('</body>',$api.'</body>',$page);
}

?>
