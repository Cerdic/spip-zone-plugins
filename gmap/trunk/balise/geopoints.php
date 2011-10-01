<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOPOINTS :
 *  Permet de compter le nombre de points sur un objet SPIP (et, éventuellement, ses déscendants)
 *
 * Paramètres :
 *	id_article/id_rubrique...	: objet à partir duquel est affichée la carte. S'il n'est pas géolocalisé, la carte n'est pas affichée, s'il l'est, la carte est centrée dessus, et il sert de base à la recherche des marqueurs
 *
 * Exemples : 
 *	#GEOPOINTS{id_document=140}
 *	#GEOPOINTS{id_document} (dans une boucle qui contient id_document dans son contexte)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_geoloc');
include_spip('balise/gmap_balises');

// Balise GEOPOINTS : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOPOINTS($p)
{
	return _gmap_calculer_balise($p, 'GEOPOINTS');
}
function balise_GEOPOINTS_stat($args, $filtres)
{
	$params = _gmap_calculer_balise_params($args);
	return array($params);
}
function balise_GEOPOINTS_dyn($params)
{
	// Init retour
	$code = "";

	// Paramètres
	$recursive = $params['recursif'] ? true : false;
	$filtre = $params['type'] ? $params['type'] : "";
	
	// Générer le résultat
	if ($params["objet"] && $params["id_objet"])
		$code = gmap_compteur($params['objet'], $params['id_objet'], $recursive, $filtre);
	
	return $code;
}

?>