<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Import des données gis / geomap
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

//
// Paramétrage des objets géolocalisables
//

function configuration_faire_editparams_dist()
{
	$result = "";
	
	// Contournement ModalBox.
	gmap_ecrire_config('gmap_edit_params', 'hack_modalbox', ((_request('hack_modalbox') === "oui") ? "oui" : "non"));
	
	// Voisins
	gmap_ecrire_config('gmap_edit_params', 'sibling_same_parent', ((_request('sibling_same_parent') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_edit_params', 'siblings_limit', _request('siblings_limit'));
	
	// Message de retour
	$msg = "";
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
