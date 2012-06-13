<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * PArtie active du formulaire du param�trage de l'interface
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function configuration_faire_markers_behavior_dist()
{
	$result = "";

	// Lire l'API utilis�e
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_interface';
	
	// Charger ce qui est sp�cifique � l'impl�mentation
	$faire_markers_behavior = charger_fonction("faire_markers_behavior", "mapimpl/".$api."/prive", true);
	if ($faire_markers_behavior)
		$msg = $faire_markers_behavior();
	
	// Message de retour
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
