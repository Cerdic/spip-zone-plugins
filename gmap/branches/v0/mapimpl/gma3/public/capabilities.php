<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Paramétrage de la carte dans l'espace public
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

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma3_public_capabilities_dist($capability)
{
	return in_array(strtolower($capability), $GLOBALS['gma3_capabilities']);
}

?>