<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Test de l'activité du plugin (par rapport à son paramétrage)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Enregistrement des paramètres passés dans la requête
function mapimpl_mxn_public_test_actif_dist()
{
	// TODO : tester si l'implémentation nécessite une clef et vérifier qu'elle est là
	return true;
}

?>