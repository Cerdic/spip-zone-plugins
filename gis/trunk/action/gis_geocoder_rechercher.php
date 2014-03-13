<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("inc/distant");

/*
 * Proxy vers le service Nomatim d'OpenStreetMap.
 *
 * Cette fonction permet de transmettre une requete auprès du service
 * de recherche d'adresse d'OpenStreetMap (Nomatim). Les arguments
 * spécifiques à SPIP sont supprimés (exec=, action= et mode=), le
 * reste est transmis tel quel à Nomatim.
 */
function action_gis_geocoder_rechercher_dist() {
	$mode = _request("mode");
	if(!$mode || !in_array($mode, array("search", "reverse")))
		return;

	/* On supprime les arguments "exec", "action" et "mode" */
	$arguments = implode("&", array_slice(explode("&", $_SERVER['QUERY_STRING']), 2));

	if($arguments) {
		header('Content-Type: application/json; charset=UTF-8');
		echo recuperer_page("http://nominatim.openstreetmap.org/{$mode}?" . $arguments);
	}
}
