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

$GLOBALS['gma2_capabilities'] = array(
		"markers",
		"LayerKML",
		"LayerAuto",
	);

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_gma2_public_capabilities_dist($capability)
{
	return in_array($capability, $GLOBALS['gma2_capabilities']);
}

?>