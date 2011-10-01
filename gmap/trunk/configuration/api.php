<?php
/*
 * GMap plugin
 * Insertion de carte sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Page de paramétrage de l'API
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

function configuration_api_dist()
{
	$corps = "";
	
	// Texte explicatif
	$corps .= '
	<div class="texte"><p>'._T("gmap:configuration_api_explic").'</p></div>';
	
	// Lire l'API utilisée
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	
	// Charger ce qui est spécifique à l'implémentation
	$show_api = charger_fonction("show_api", "mapimpl/".$api."/prive");
	$corps .= $show_api();
	
	return gmap_formulaire_submit('configuration_api', $corps, find_in_path('images/logo-config-api.png'), _T('gmap:configuration_api'));
}

?>
