<?php
/*
 * Plugin GMap
 * G�olocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOMAP :
 *  Insertion d'une carte depuis un squelette ou un mod�le.
 *
 * Param�tres :
 *	map			: id de la carte cible (1 si omis, ce qui est le d�faut de toutes les balises)
 *	id_article/id_rubrique...	: objet � partir duquel est affich�e la carte. S'il n'est pas g�olocalis�, la carte n'est pas affich�e, s'il l'est, la carte est centr�e dessus, et il sert de base � la recherche des marqueurs
 *	markers		: d�finition des marqueurs (local : seul(s) le(s) marqueur(s) de l'objet sont affich�s; childs : les marqueurs de l'objet et de ses descendants de niveau 1 sont affich�s; recursive : les marqueurs de l'objet et de tous ses descendants sont affich�s; query : la liste des marqueurs provient d'une requ�te ajax)
 *	query		: nom du fichier ajax de requ�te (si markers=query)
 *  viewport	: objet qui sert de r�f�rence pour le positionnement et le zoom initial de la carte
 *	latitude	: latitude du centre de la carte
 *	longitude	: longitude du centre de la carte
 *	zoom		: zoom initial de la carte
 *	fond		: fond de carte par d�faut(plan / satellite / mixte / physic / earth)
 * ... une multitude d'autres param�tres d�pendants plus ou moins de l'impl�mentation de la carte (Google Maps V2/V3, autre ?)
 *
 * Exemples : 
 *	#GEOMAP{id_document=140}
 *	#GEOMAP{id_document} (dans une boucle qui contient id_document dans son contexte)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');
include_spip('balise/gmap_balises');

// Balise GEOMAP : renvoie les informations sur le marqueur associ� � un point sur un objet
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

	// D�codage des param�tres
	$mapId = $GLOBALS['nextMapID']++;
	$GLOBALS['currentMapID'] = $mapId;
	if ($params['map'])
		$mapId = $params['map'];

	// Ajout d'une div si n�cessaire
	$bCreateDiv = (isset($params['creatediv']) && $params['creatediv']) ? true : false;
	if ($bCreateDiv)
	{
		$width = $params['width'] ? $params['width'] : "100%";
		$height = $params['height'] ? $params['height'] : "400px";
		$code .= '
<div style="display: block; position: relative; width: '.$width.'; height: '.$height.';">
';
	}

	// G�n�rer le r�sultat
	$code .= gmap_ajoute_carte_public($params['objet'], $params['id_objet'], $mapId, $params);
	
	// Fermer la div
	if ($bCreateDiv)
		$code .= '
</div>
';
	
	return $code;
}

?>