<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Balise GEOMARKERICONS :
 *  La balise utilise les champs objet, id_objet et type_poitn qui doivent être
 *  dans le contexte, c'est le cas quand la balise est insérée dans une boucle
 *  GEOPOINTS.
 * Paramètres :
 *	format :	format de la sortie (actuellement seul "kml" est supporté, par défaut kml
 *	prefix :	préfixe des noms de balise (espace de nom), par défaut gmm
 *  tag	   : 	forme courte (short) ou longue (long), par défaut short
 * Exemple : 
 * 	[(#GEOMARKERICONS{format=kml,prefix=gmm,tag=short})]
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');

// Globales
$GLOBALS['iconsAliases'] = array();
$GLOBALS['iconsDefs'] = array();

// Balise GEOMARKERICONS : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOMARKERICONS($p)
{
	$args = array("objet", "id_objet", "type_point", "id_point");
	return calculer_balise_dynamique(
			$p,				//  le nœud AST pour la balise
			'GEOMARKERICONS',	//  le nom de la balise
			$args);  //  les éléments utilisables de l'environnement	
}
function  balise_GEOMARKERICONS_stat($args, $filtres)
{
	// Récupérer les paramètres
	$objet = $args[0];
	$id_objet = $args[1];
	$type = $args[2];
	$id_point = $args[3];
	$format = "kml";
	$prefix = "gmm";
	$tag = "short";
	$incComplete = true;
	$incNormal = true;
	$incShadow = true;
	$incSelected = true;
	for ($index = 4; $index < count($args); $index++)
	{
		// Décodage des arguments
		list($key, $value) = _gmap_split_param($args[$index]);
		if (!$key)
			continue;
		
		// Traitement
		if (strcasecmp($key, "format") == 0)
			$format = $value;
		else if (strcasecmp($key, "prefix") == 0)
			$prefix = $value;
		else if (strcasecmp($key, "tag") == 0)
			$tag = $value;
		else if (strcasecmp($key, "complete") == 0)
			$incComplete = ($value == 'non') ? false : true;
		else if (strcasecmp($key, "normal") == 0)
			$incNormal = ($value == 'non') ? false : true;
		else if (strcasecmp($key, "shadow") == 0)
			$incShadow = ($value == 'non') ? false : true;
		else if (strcasecmp($key, "selected") == 0)
			$incSelected = ($value == 'non') ? false : true;
	}
		
	// Rechercher le fichier qui correspond à ce marqueur
	$contexte = array();
	if ($objet && strlen($objet) && $id_objet)
	{
		$contexte['objet'] = $objet;
		$contexte['id_objet'] = $id_objet;
		$contexte['id_'.$objet] = $id_objet;
	}
	if ($type && strlen($type))
		$contexte['type_point'] = $type;
	if ($id_point)
		$contexte['id_point'] = $id_point;
	$icon = gmap_trouve_def_file($contexte, 'gmap-marker', 'gmd', gmap_theme_folder(), $GLOBALS['iconsAliases']);
	if (isset($icon['file']))
	{
		$icons = gmap_parse_icone_def_file($icon['file']);
		$folder = gmap_theme_folder();
		if ($icon['file'] && $icon['buffer'])
			$GLOBALS['iconsAliases'][$icon['buffer']] = $icon['name'];
	
		// Post-traitement des icones
		if (!$incComplete || !$incNormal || !$incShadow || !$incSelected)
		{
			foreach ($icons as $index => $iconDef)
			{
				// Pour alléger les modèles, supprimer ce qui n'est pas demandé
				if ((!$incComplete && ($iconDef['type'] === 'complete')) ||
					(!$incNormal && (!isset($iconDef['type']) || ($iconDef['type'] === 'simple'))) ||
					(!$incShadow && ($iconDef['type'] === 'shadow')) ||
					(!$incSelected && ($iconDef['state'] === 'selected')))
					unset($icons[$index]);
			}
		}
	}
	else
	{
		$icons = null;
		$folder = '';
	}
	
	// Renvoyer les paramètres de la partie dynamique
	return array($icons, $icon['name'], $objet, $id_objet, $type, $format, $prefix, $tag, $folder);
}
function balise_GEOMARKERICONS_dyn($icons, $name, $objet, $id_objet, $type, $format, $prefix, $tag, $folder)
{
	$env = array('icons'=>$icons, 'name'=>$name, 'prefix'=>$prefix, 'folder'=>$folder);
	if ($format === "kml")
	{
		if ($tag === "long")
			return array('modeles/icons_marker', 0, $env);
		else
			return array('modeles/icons_short_marker', 0, $env);
	}
	else if ($format === "json")
		return array('modeles/icons_json_marker', 0, $env);
}

?>