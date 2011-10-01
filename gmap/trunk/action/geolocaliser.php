<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Exécution des actions de géolocalisation
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

// Exécution de l'action liée à une édition de la partie privée par formulaire ajax
function action_geolocaliser_dist()
{
	// Récupérer l'action à effectuer
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// Redirection vers exec/geolocaliser.php, qui chargera la fonction adéquate de formulaires et renverra le texte du formulaire
	$page = rawurldecode(_request('redirect'));
	$page = parametre_url($page, 'edition', $arg, "&");
	$page = parametre_url($page, 'geoobject', _request('geoobject'), "&");
	$page = parametre_url($page, 'geoobject_id', _request('geoobject_id'), "&");
	$page = parametre_url($page, 'source_exec', _request('source_exec'), "&");
	$page = parametre_url($page, 'ui_state', _request('ui_state'), "&");
	
	// Effectuer l'action
	$action = charger_fonction('faire_geolocaliser', 'formulaires');
	if ($action)
	{
		if ($result = $action())
			$page = gmap_encode_result($page, $result);
	}
	
	// Rediriger sur exec/editer_gmap
	redirige_par_entete($page);
}

?>
