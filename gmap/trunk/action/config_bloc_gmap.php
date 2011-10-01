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
	$page = parametre_url($page, 'configuration', $arg,"&");
	$action = charger_fonction('faire_'.$arg, 'configuration');
	if ($action)
	{
		if ($result = $action())
			$page = gmap_encode_result($page, $result);
	}
	redirige_par_entete($page);
}

?>
