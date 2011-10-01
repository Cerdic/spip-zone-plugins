<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOKML :
 *  Insertion d'un fichier KML depuis un squelette ou un modèle.
 *
 * Paramètres :
 *	map			: id de la carte cible (1 si omis, ce qui est aussi le défaut de #GEOMAP)
 *	show		: true pour que le fichier soit visible dès sa création (par défaut), false sinon
 *	url			: url du fichier KML (ou KMZ)
 *	id			: identifiant de la couche, si omis un hashcode est généré à partir de l'URL
 *
 * Exemples : 
 *  #GEOKML{url=...}
 *	#GEOKML{id_document=140}
 *	#GEOKML{id_document} (dans une boucle qui contient id_document dans son contexte)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');
include_spip('balise/gmap_balises');

// Balise GEOKML : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOKML($p)
{
	return _gmap_calculer_balise($p, 'GEOKML');
}
function balise_GEOKML_stat($args, $filtres)
{
	$params = _gmap_calculer_balise_params($args);
	return array($params);
}
function balise_GEOKML_dyn($params)
{
	// Init retour
	$code = "";
		
	// Décodage des paramètres
	$mapId = $GLOBALS['currentMapID'];
	if ($params['map'])
		$mapId = $params['map'];
	$show = (($params['show'] === "non") || ($params['show'] === "false") || ($params['show'] === "faux")) ? false : true;
		
	// Si un objet de type document est spécifié
	if (isset($params['objet']) && ($params['objet'] === "document"))
		$code = gmap_ajoute_kml($params['id_objet'], $mapId, $show);
	
	// Sinon, il faut une URL
	else if (isset($params['url']) && is_string($params['url']) && strlen($params['url']))
	{
		$url = $params['url'];
		if ($params['id'])
			$kmlId = $params['id'];
		else
			$kmlId = "url_".bin2hex(mhash(MHASH_MD5, $url));
		$code = gmap_ajoute_kml_url($kmlId, $url, $mapId, $show);
	}
	
	return $code;
}

?>