<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Balise GEOPOPUP :
 * Cette balise ne peut s'utilsier que dans une boucle GEOPOINTS, à défaut les 
 * champs objet, id_objet et type_point devraient être précisés en paramètre.
 *
 * Paramètres :
 * Aucun paramètres connus.
 *
 * Exemple : 
 * 	[(#GEOPOPUP)]
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc');

// Balise GEOPOPUP : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOPOPUP($p)
{
	$args = array("objet", "id_objet", "type_point");
	return calculer_balise_dynamique(
			$p,				//  le nœud AST pour la balise
			'GEOPOPUP',	//  le nom de la balise
			$args);  //  les éléments utilisables de l'environnement	
}
function  balise_GEOPOPUP_stat($args, $filtres)
{
	// Récupérer les paramètres
	$objet = $args[0];
	$id_objet = $args[1];
	$type = $args[2];
	$objet_parent = "";
	$id_objet_parent = 0;
	for ($index = 3; $index < count($args); $index++)
	{
		// Décodage des arguments
		list($key, $value) = _gmap_split_param($args[$index]);
		if (!$key)
			continue;
		
		// Traitement
		if (strcasecmp($key, "objet_parent") == 0)
			$objet_parent = $value;
		else if (strcasecmp($key, "id_objet_parent") == 0)
			$id_objet_parent = $value;
	}
	
	// Récupérer le squelette approprié
	$fond = gmap_trouve_def_file($objet, $id_objet, $type, 'gmap-info-', 'html');
	
	// Renvoyer vers la partie dynamique
	return array($fond['spip-path'], $objet, $id_objet, $type, $objet_parent, $id_objet_parent);
}
function balise_GEOPOPUP_dyn($fond, $objet, $id_objet, $type, $objet_parent, $id_objet_parent)
{
	$env = array('objet'=>$objet, 'id_objet'=>$id_objet, 'type_point'=>$type, 'id_'.$objet=>$id_objet, 'objet_parent'=>$objet_parent, 'id_objet_parent'=>$id_objet_parent);
	return array($fond, 0, $env);
}

?>