<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Balise GEOMARKERICONS :
 *  La balise utilise les champs objet, id_objet et type_poitn qui doivent �tre
 *  dans le contexte, c'est le cas quand la balise est ins�r�e dans une boucle
 *  GEOPOINTS.
 * Param�tres :
 *	format :	format de la sortie (actuellement seul "kml" est support�, par d�faut kml
 *	prefix :	pr�fixe des noms de balise (espace de nom), par d�faut gmm
 *  tag	   : 	forme courte (short) ou longue (long), par d�faut short
 * Exemple : 
 * 	[(#GEOMARKERICONS{format=kml,prefix=gmm,tag=short})]
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');

// Globales
$GLOBALS['iconsAliases'] = array();
$GLOBALS['iconsDefs'] = array();

// Balise GEOMARKERICONS : renvoie les informations sur le marqueur associ� � un point sur un objet
function balise_GEOMARKERICONS($p)
{
	$args = array("objet", "id_objet", "type_point");
	return calculer_balise_dynamique(
			$p,				//  le n�ud AST pour la balise
			'GEOMARKERICONS',	//  le nom de la balise
			$args);  //  les �l�ments utilisables de l'environnement	
}
function  balise_GEOMARKERICONS_stat($args, $filtres)
{
	// R�cup�rer les param�tres
	$objet = $args[0];
	$id_objet = $args[1];
	$type = $args[2];
	$format = "kml";
	$prefix = "gmm";
	$tag = "short";
	for ($index = 3; $index < count($args); $index++)
	{
		// D�codage des arguments
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
	}
		
	// Rechercher le fichier qui correspond � ce marqueur
	$icon = gmap_trouve_def_file($objet, $id_objet, $type, 'gmap-marker-', 'gmd', $GLOBALS['iconsAliases']);
	if (isset($icon['file']))
	{
		$icons = gmap_parse_icone_def_file($icon['file']);
		$id_rubrique = gmap_get_rubrique($objet, $id_objet);
		$GLOBALS['iconsAliases'][$objet.'-'.$type.'-'.$id_rubrique] = $icon['name'];
	}
	else
		$icons = NULL;
	
	// Renvoyer les param�tres de la partie dynamique
	return array($icons, $icon['name'], $objet, $id_objet, $type, $format, $prefix, $tag);
}
function balise_GEOMARKERICONS_dyn($icons, $name, $objet, $id_objet, $type, $format, $prefix, $tag)
{
	$env = array('icons'=>$icons, 'name'=>$name, 'prefix'=>$prefix);
	if ($tag === "long")
		return array('modeles/icons_marker', 0, $env);
	else
		return array('modeles/icons_short_marker', 0, $env);
}

?>