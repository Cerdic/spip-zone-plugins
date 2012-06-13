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
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_spip_utils');

// Globales
$GLOBALS['iconsAliases'] = array(); // ici, on peut avoir un seul buffer : il est valable sur une requête unique, donc pour une seule carte
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
	$incSelected = (gmap_lire_config('gmap_optimisations', 'gerer_selection', 'oui') === 'oui') ? true : false;
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
	$branches = (gmap_lire_config('gmap_optimisations', 'gerer_branches', 'oui') === 'oui') ? true : false;
	$icon = gmap_trouve_def_file($contexte, 'gmap-marker', 'gmd', array(
									'branches'=>$branches,
									'sous-dossier'=>gmap_theme_folder(),
									'buffer-aval'=>$GLOBALS['iconsAliases']));
	
	// Gérer le buffer
	if ($icon['file'] && $icon['buffer'])
		$GLOBALS['iconsAliases'][$icon['buffer']] = $icon['name'];
		
	// Éviter de mettre plusieurs fois la même icone dans le fichier
	// Le buffer des icones ne suffit pas puisqu'il est index sur un nom complet :
	// dans le cas des rubriques, on les créé toujours !
	if ($GLOBALS['iconsDefs'][$icon['name']]) // Déjà défini dans cette session
		unset($icon['file']);
	else
		$GLOBALS['iconsDefs'][$icon['name']] = true;
		
	// Parser le fichier des icones
	if (isset($icon['file']))
	{
		$icons = gmap_parse_icone_def_file($icon['file']);
		$folder = gmap_theme_folder();
			
		// Pour alléger les modèles, supprimer ce qui n'est pas demandé
		if (!$incComplete || !$incNormal || !$incShadow || !$incSelected)
		{
			foreach ($icons as $index => $iconDef)
			{
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
	return gmap_geomarkericons($icons, $icon['name'], $objet, $id_objet, $type, $format, $prefix, $tag, $folder);
}
// Pas de partie dynamique : on calcule tout avant le cache

function gmap_geomarkericons($icons, $name, $objet, $id_objet, $type, $format, $prefix, $tag, $folder)
{
	// Quel fond ?
	$fond = null;
	if ($format === "kml")
	{
		if ($tag === "long")
			$fond = 'modeles/icons_marker';
		else
			$fond = 'modeles/icons_short_marker';
	}
	else if ($format === "json")
		$fond = 'modeles/icons_json_marker';
	if (!$fond)
		return '';
		
	$env = array('icons'=>$icons, 'name'=>$name, 'prefix'=>$prefix, 'folder'=>$folder);
	return gmap_recuperer_fond($fond, $env);
}

?>