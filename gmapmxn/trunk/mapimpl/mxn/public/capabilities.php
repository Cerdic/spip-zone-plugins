<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/provider_caps');

$GLOBALS['mxn_capabilities'] = array(
		"markers",
		"infowindows",
//		"layerkml",  	=> selon fournisseur
//		"layerauto", 	=> non
//		"layeractions", => non
//		"geocoder",  	=> selon fournisseur
//		"dragmarkers",	=> selon fournisseur
//		"dblclick",		=> non
	);

function mapimpl_mxn_public_capabilities_dist($capability)
{
	$caps = gmapmxn_getCaps();
	$capability = strtolower($capability);
	if ($capability == "layerkml")
		return ($caps['kml'] == 'oui') ? true : false;
	else if ($capability == "geocoder")
		return ($caps['geocoder'] == 'oui') ? true : false;
	else if ($capability == "dragmarkers")
		return ($caps['drag_markers'] == 'oui') ? true : false;
	else
		return in_array($capability, $GLOBALS['mxn_capabilities']);
}

?>