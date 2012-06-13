<?php
/*
 * Plugin GMap
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Page de paramétrage du plugin : interface des marqueurs
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_saisie_utils');

function configuration_markers_behavior_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// Lire l'API utilisée
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_interface';
	
	// Charger ce qui est spécifique à l'implémentation
	$show_markers_behavior = charger_fonction("show_markers_behavior", "mapimpl/".$api."/prive", true);
	$code = "";
	if ($show_markers_behavior)
		$code .= $show_markers_behavior();
	if (strlen($code) == 0)
		return ""; // pour éviter d'avoir un cadre vide...
	$corps .= $code;
		
	// Renvoyer le formulaire
	return gmap_formulaire_ajax('config_bloc_gmap', 'markers_behavior', 'configurer_gmap_ui', $corps,
		find_in_path('images/logo-config-markers_behavior.png'),
		_T('gmap:configuration_markers_behavior'));
}



?>
