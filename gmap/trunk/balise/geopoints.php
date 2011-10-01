<?php
/*
 * Plugin GMap
 * G�olocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOPOINTS :
 *  Permet de compter le nombre de points sur un objet SPIP (et, �ventuellement, ses d�scendants)
 *
 * Param�tres :
 *	id_article/id_rubrique...	: objet � partir duquel est affich�e la carte. S'il n'est pas g�olocalis�, la carte n'est pas affich�e, s'il l'est, la carte est centr�e dessus, et il sert de base � la recherche des marqueurs
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

// Balise GEOPOINTS : renvoie les informations sur le marqueur associ� � un point sur un objet
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

	// Param�tres
	$recursive = $params['recursif'] ? true : false;
	$filtre = $params['type'] ? $params['type'] : "";
	
	// G�n�rer le r�sultat
	if ($params["objet"] && $params["id_objet"])
		$code = gmap_compteur($params['objet'], $params['id_objet'], $recursive, $filtre);
	
	return $code;
}

?>