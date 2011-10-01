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

function configuration_faire_rubgeo_dist()
{
	$result = "";
	
	// Types d'objets
	gmap_ecrire_config('gmap_objets_geo', 'type_rubriques', ((_request('choix_type_rubrique') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_objets_geo', 'type_articles', ((_request('choix_type_article') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_objets_geo', 'type_documents', ((_request('choix_type_document') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_objets_geo', 'type_breves', ((_request('choix_type_breve') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_objets_geo', 'type_mots', ((_request('choix_type_mot') === "oui") ? "oui" : "non"));
	gmap_ecrire_config('gmap_objets_geo', 'type_auteurs', ((_request('choix_type_auteur') === "oui") ? "oui" : "non"));

	// Tout le site
	gmap_ecrire_config('gmap_objets_geo', 'tout_le_site', ((_request('tout_le_site') === "oui") ? "oui" : "non"));
	
	// Liste des rubriques
	$simple_rubs = array();
	if ($rubgeo = _request('les_rubriques'))
	{
		foreach ($rubgeo as $rub)
		{
			$parts = explode('|', $rub);
			if ($parts[1])
				$simple_rubs[] = $parts[1];
		}
		$strrubs = implode(',', $simple_rubs);
	}
	gmap_ecrire_config('gmap_objets_geo', 'liste', $simple_rubs);
	
	// Message de retour
	$msg = "";
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
