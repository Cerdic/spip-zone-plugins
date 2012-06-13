<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Param�trage de la carte dans l'espace public
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['gma3_capabilities'] = array(
		"markers",
		"infowindows",
		"layerkml",
//		"layerauto",		=> non
		"layeractions",
		"geocoder",
		"dragmarkers",
		"dblclick",
	);

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_gma3_public_capabilities_dist($capability)
{
	return in_array(strtolower($capability), $GLOBALS['gma3_capabilities']);
}

?>