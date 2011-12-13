<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Initialisation de paramètres des optimisations
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_init_optimisations_dist()
{
	gmap_init_config('gmap_optimisations', 'gerer_selection', 'oui');
	gmap_init_config('gmap_optimisations', 'gerer_branches', 'oui');
}

?>
