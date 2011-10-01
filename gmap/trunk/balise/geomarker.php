<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOMARKER :
 *  Insertion d'un marqueur depuis un squelette ou un modèle.
 *
 * Paramètres :
 *	map			: id de la carte cible (1 si omis, ce qui est aussi le défaut de #GEOMAP)
 * - Dans le cas d'un marqueur sur un objet géolocalisé du site :
 *	id_rubrique|id_article|id_breve|id_document|id_auteur|id_mot|{objet, id_objet} : objet duquel on affiche UN maruquer (le meilleur s'il y en a plusieurs)
 *	type : restriction du type de marqueur (sur un objet)
 * - Dans le cas d'un marqueur "libre" :
 *	latitude	: latitude
 *	longitude	: longitude
 *	titre		: titre (affiché en survol + dans la bulle)
 *	texte		: corps de texte dans la bulle
 *	icon		: nom d'un fichier .gmd contenant la définition de l'icone
 *
 * Exemples : 
 * 	#GEOMARKER{latitude=-36, longitude=141}
 * 	#GEOMARKER{id_auteur=10}
 * 	#GEOMARKER{id_article} (dans une boucle qui contient id_article dans son contexte)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');
include_spip('balise/gmap_balises');

// Balise GEOMARKER : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOMARKER($p)
{
	return _gmap_calculer_balise($p, 'GEOMARKER');
}
function balise_GEOMARKER_stat($args, $filtres)
{
	$params = _gmap_calculer_balise_params($args, true);
	return array($params);
}
function balise_GEOMARKER_dyn($params)
{
	// Init retour
	$code = "";
		
	// Décodage des paramètres
	$mapId = $GLOBALS['currentMapID'];
	if ($params['map'])
		$mapId = $params['map'];
	
	// S'il y a un objet, procédure d'ajout classique
	if ($params['objet'] && $params['id_objet'])
	{
		$type = "";
		if ($params['type'])
			$type = $params['type'];
		$code = gmap_ajoute_marqueur_site($params['objet'], intval($params['id_objet']), $mapId, $type);
	}
	
	// Sinon ajouter un marqueur manuel
	else if ($params['latitude'] && $params['longitude'])
	{
		$latitude = $params['latitude'];
		$longitude = $params['longitude'];
		if ($params['id'])
			$markerId = $params['id'];
		else
			$markerId = "marker_".$latitude."_".$longitude;
		$code = gmap_ajoute_marqueur_special($markerId, $latitude, $longitude, $mapId, $params['titre'], $params['texte'], $params['icon']);
	}
	
	return $code;
}

?>