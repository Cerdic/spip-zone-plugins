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
include_spip('gmap_filtres');

// Balise GEOPOPUP : renvoie les informations sur le marqueur associé à un point sur un objet
function balise_GEOPOPUP($p)
{
	$args = array("objet", "id_objet", "type_point", "id_point");
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
	$id_point = $args[3];
	$objet_parent = "";
	$id_objet_parent = 0;
	$contenu_seul = false;
	$json = false;
	for ($index = 4; $index < count($args); $index++)
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
		else if (strcasecmp($key, "contenu_seul") == 0)
			$contenu_seul = ($value === 'oui') ? true : false;
		else if (strcasecmp($key, "json") == 0)
			$json = ($value === 'oui') ? true : false;
	}
	
	// Récupérer le squelette approprié
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
	$fond = gmap_trouve_def_file($contexte, 'gmap-info', 'html', $branches, 'modeles');
	
	// Renvoyer vers la partie dynamique
	//return array($fond['spip-path'], $objet, $id_objet, $type, $objet_parent, $id_objet_parent, $contenu_seul, $json);
	return gmap_geopopup($fond['spip-path'], $objet, $id_objet, $type, $objet_parent, $id_objet_parent, $contenu_seul, $json);
}
// Pas de partie dynamique : on calcule tout avant le cache

// Récupération du contenu de la bulle
function gmap_geopopup($fond, $objet, $id_objet, $type, $objet_parent, $id_objet_parent, $contenu_seul, $json)
{
	$env = array('objet'=>$objet, 'id_objet'=>$id_objet, 'type_point'=>$type, 'id_'.$objet=>$id_objet, 'objet_parent'=>$objet_parent, 'id_objet_parent'=>$id_objet_parent);
	$return = recuperer_fond($fond, $env);
	if ($contenu_seul || $json)
	{
		if ($contenu_seul)
			$return = html_body($return);
		if ($json)
			$return = texte_json($return);
		else
			$return = protege_html($return);
	}
	return $return;
}

?>