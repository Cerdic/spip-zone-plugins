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

$GLOBALS['gma2_capabilities'] = array(
		"markers",
		"LayerKML",
		"LayerAuto",
	);

// Enregistrement des paramètres passés dans la requête
function mapimpl_gma2_public_capabilities_dist($capability)
{
	return in_array($capability, $GLOBALS['gma2_capabilities']);
}

?>