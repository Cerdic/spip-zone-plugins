<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Test de l'activit� du plugin (par rapport � son param�trage)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des param�tres pass�s dans la requ�te
function mapimpl_gma2_public_test_actif_dist()
{
	// Il faut une clef
	$key = gmap_lire_config('gmap_api_gma2', 'key');
	return ($key && ($key != "")) ? true : false;
}

?>