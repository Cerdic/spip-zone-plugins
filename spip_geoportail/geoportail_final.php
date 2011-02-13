<?php

/** Recalcule sur chaque page (hors cache) 
*/
function geoportail_affichage_final($page)
{	// on regarde rapidement si la page inclus un geoportail
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_')===FALSE) return $page;

	// Inclure le hash code de maniere dynamique
	charger_fonction('securiser_action','inc');
	$action = calculer_action_auteur('geoportail');
	
	$engine=
'<script>jQuery.geoportail.hash = "'.$action.'";</script>
<script language=javascript>jQuery(document).ready(	function() { jQuery.geoportail.initMap("'._DIR_PLUGIN_GEOPORTAIL.'"); });</script>

<!-- API Geoportail -->
<script type="text/javascript" src="http://api.ign.fr/geoportail/api/js/'._API_GEOPORTAIL_VERSION.'/GeoportalExtended.js" charset=utf-8>// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Layer/Locator.js">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Format/Ceoconcept_rip.js">// <![CDATA[
    // ]]></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/Layer/GXT.js">// <![CDATA[
    // ]]></script>

';
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_GMAP-->'))
		$engine .= '<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>';
	if (strpos($page, '<!--_SPIP_GEOPORTAIL_YHOO-->'))
		$engine .= '<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers"></script>';
		
	// Inclure l'API dans le Header
	return preg_replace('/<!--_GEOPORTAIL_HEADER_-->/', $engine, $page, 1);
}

?>