<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Exécution des actions de paramétrage
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function action_config_bloc_gmap_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$page = rawurldecode(_request('redirect'));
	$page = parametre_url($page, 'configuration', $arg, "&");
	if ($arg == 'map_defaults') // pas beau... mais je suis un peu fatigué
		$page = parametre_url($page, 'map_defaults_profile', _request('map_defaults_profile'), "&");
	$action = charger_fonction('faire_'.$arg, 'configuration', true);
	if ($action)
	{
		if ($result = $action())
			$page = gmap_encode_result($page, $result);
	}
	redirige_par_entete($page);
}

?>
