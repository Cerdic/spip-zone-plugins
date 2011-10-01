<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOMAP :
 *  Insertion d'une carte depuis un squelette ou un modèle.
 *
 * Paramètres :
 *	map			: id de la carte cible (1 si omis, ce qui est le défaut de toutes les balises)
 *	id_article/id_rubrique...	: objet à partir duquel est affichée la carte. S'il n'est pas géolocalisé, la carte n'est pas affichée, s'il l'est, la carte est centrée dessus, et il sert de base à la recherche des marqueurs
 *	markers		: définition des marqueurs (local : seul(s) le(s) marqueur(s) de l'objet sont affichés; childs : les marqueurs de l'objet et de ses descendants de niveau 1 sont affichés; recursive : les marqueurs de l'objet et de tous ses descendants sont affichés; query : la liste des marqueurs provient d'une requête ajax)
 *	query		: nom du fichier ajax de requête (si markers=query)
 *	latitude	: latitude du centre de la carte
 *	longitude	: longitude du centre de la carte
 *	zoom		: zoom initial de la carte
 *	fond		: fond de carte par défaut(plan / satellite / mixte / physic / earth)
 * ... une multitude d'autres paramètres dépendants plus ou moins de l'implémentation de la carte (Google Maps V2/V3, autre ?)
 *
 * Exemples : 
 *	#GEOMAP{id_document=140}
 *	#GEOMAP{id_document} (dans une boucle qui contient id_document dans son contexte)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');
include_spip('balise/gmap_balises');

// Balise GEOMAP : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOMAP($p)
{
	return _gmap_calculer_balise($p, 'GEOMAP');
}
function balise_GEOMAP_stat($args, $filtres)
{
	$params = _gmap_calculer_balise_params($args);
	return array($params);
}
function balise_GEOMAP_dyn($params)
{
	// Init retour
	$code = "";

	// Décodage des paramètres
	$mapId = $GLOBALS['nextMapID']++;
	$GLOBALS['currentMapID'] = $mapId;
	if ($params['map'])
		$mapId = $params['map'];

	// Ajout d'une div si nécessaire
	$bCreateDiv = (isset($params['creatediv']) && $params['creatediv']) ? true : false;
	if ($bCreateDiv)
	{
		$width = $params['width'] ? $params['width'] : "100%";
		$height = $params['height'] ? $params['height'] : "400px";
		$code .= '
<div style="display: block; position: relative; width: '.$width.'; height: '.$height.';">
';
	}

	// Générer le résultat
	$code .= gmap_ajoute_carte_public($params['objet'], $params['id_objet'], $mapId, $params);
	
	// Fermer la div
	if ($bCreateDiv)
		$code .= '
</div>
';
	
	return $code;
}

?>