<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Partie active du formulaire du param�trage des optimisations
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_faire_optimisations_dist()
{
	$result = "";
	
	// R�cup�rer les param�tres et sauvegarder
	gmap_ecrire_config('gmap_optimisations', 'gerer_selection', ((_request('gerer_selection') === 'oui') ? 'oui' : 'non'));
	gmap_ecrire_config('gmap_optimisations', 'gerer_branches', ((_request('gerer_branches') === 'oui') ? 'oui' : 'non'));
	
	// Message de retour
	$msg = "";
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
