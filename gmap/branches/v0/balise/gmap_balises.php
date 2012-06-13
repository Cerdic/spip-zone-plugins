<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Outils pour la gestion des balises et balises statiques (les plus simples, pour
 * les balises plus compliquées au niveau des arguments, le modèle des balises
 * dynamiques de SPIP est plus approprié car il prend en charge le décodage des
 * paramètres.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_geoloc');


// Mécanisme d'allocation automatique des numéros de carte
$GLOBALS['currentMapID'] = 1;
$GLOBALS['nextMapID'] = 1;


/* Outils pour gérer facilement les balises dynamiques dans le contexte de GMap
 * Les besoins sont un peu toujours les mêmes : on prend du contexte ou des
 * paramètres un id d'objet géolocalisable et les balises acceptent d'autres paramètres
 */

$GLOBALS['context_query'] = array(
	'objet', 'id_objet',
	'id_document',
	'id_article',
	'id_breve',
	'id_rubrique',
	'id_secteur',
	'id_mot',
	'id_auteur',
	'type_point'
);

// Au départ : lancer la balise dynamique
function _gmap_calculer_balise($p, $nom)
{
	return calculer_balise_dynamique(
			$p,		//  le nœud AST pour la balise
			$nom,	//  le nom de la balise
			$GLOBALS['context_query']); //  les éléments utilisables de l'environnement	
}

// Décomposition d'un paramètre en paire clef/valeur, selon qu'il y a ou non
// un signe '=' dans la chaine...
function _gmap_split_param($param)
{
	$key = null;
	$value = null;
	if (($pos = strpos($param, "=")) !== false)
	{
		$key = substr($param, 0, $pos);
		$value = trim(substr($param, $pos+1));
		if (!$value || !strlen($value)) // cas de l'appel depuis les modèles où l'on a '$key='
			$key = null;
	}
	else
	{
		$key = $param;
		$value = true;
	}
	if (is_string($key))
		$key = trim($key);
	if (!$key || !strlen($key))
		$value = null;
	return array($key, $value);
}

// Dans la partie statique, décoder les paramètres
// Renvoie un tableau de paramètres nommés contenant :
// - toujours "objet" et "id_objet"
// - "type_point", une chaine vide s'il n'est pas défini
// - tous les autres paramètres passés à la balise
function _gmap_calculer_balise_params($args, $bStrictParams = false)
{
	// Commencer par décoder le paramètres explicites, donc ceux qui sont après ce
	// qui a été demandé dans la pile
	$params = array();
	for ($index = count($GLOBALS['context_query']); $index < count($args); $index++)
	{
		list($key, $value) = _gmap_split_param($args[$index]);
		if (!$key)
			continue;
		$params[$key] = $value;
	}
	
	// Si une requête est précisée, désactiver la recherche d'un point de 
	// référence à tout prix : la personne qui a donné la requête doit 
	// savoir quels paramètres il faut passer
	if (isset($params['query']))
		$bStrictParams = true;
	
	// Inialiser les paramètres implicites
	$objet = null;
	$id_objet = null;
	$type_point = null;
	
	// Traiter le cas "type_point" en premier, il simplifiera la suite
	if (isset($params["type_point"]))
	{
		if ($params["type_point"] === true)
			$type_point = $args[array_search("type_point", $GLOBALS['context_query'], true)];
		else
			$type_point = $params["type_point"];
		unset($params["type_point"]);
	}
	
	// Traiter le cas de "objet" dont peut dépendre le type d'objet choisit
	if (isset($params["objet"]))
	{
		if (is_string($params["objet"]))
			$objet = $params["objet"];
		unset($params["objet"]);
	}
	
	// Finalement, traiter le cas de id_objet qui est prioritaire, mais
	// seulement si $objet est déjà défini
	if (isset($params["id_objet"]))
	{
		if ($objet && is_string($params["id_objet"]))
			$id_objet = $params["id_objet"];
		unset($params["id_objet"]);
	}
	
	// Là, on a nettoyé les paramètres passés manuellement ($params) de "objet", "id_objet" et
	// "type_point", si on n'a trouvé aucun objet, on va essayer d'en trouvé un dans la pile
	
	// Si on n'a pas de id_objet, mais un objet désigné, le chercher
	if (!$id_objet && is_string($objet))
	{
		$idName = 'id_'.$objet;
		if (isset($params[$idName]) && is_string($params[$idName]))
			$id_objet = $params[$idName];
		else if ($index = array_search($idName, $GLOBALS['context_query'], true))
			$id_objet = $args[$index];
	}
	
	// Si on n'a pas de id_objet, parcourir les paramètres explicites pour voir si
	// un type d'objet est désigné (c'est le cas le plus courant : on a mis dans les
	// paramètre {id_rubrique} ou {id_rubrique=XX})
	if (!$id_objet)
	{
		// Rechercher le premier paramètre qui correspond à un des paramètres implicites
		foreach ($params as $key => $value)
		{
			if (($key === "objet") || ($key === "id_objet") || ($key === "type_point"))
				continue;
			if (in_array($key, $GLOBALS['context_query'], true))
			{
				if (preg_match("/^id_([a-z]*)$/i", $key, $matches))
				{
					$objet = $matches[1];
					if ($value === true)
						$id_objet = $args[array_search($key, $GLOBALS['context_query'], true)];
					else
						$id_objet = $value;
					break;
				}
			}
		}
	}
	
	// Si on n'a toujours rien, parcourir les paramètres implicites dans l'ordre
	// de préférence
	// Ici, il serait mieux de prendre la boucle la plus proche, mais je ne sais pas
	// faire ça dans une balise dynamique : il faudrait décoder les codes PHP renvoyés
	// pour les valeurs afin de voir lequel a l'indice le plus proche
	if (!$id_objet && ($bStrictParams !== true))
	{
		foreach ($GLOBALS['context_query'] as $index => $name)
		{
			if (($name === "objet") || ($name === "id_objet") || ($name === "type_point"))
				continue;
			$value = $args[$index];
			if (isset($value) && is_string($value) && strlen($value))
			{
				if (preg_match("/^id_([a-z]*)$/i", $name, $matches))
				{
					$objet = $matches[1];
					$id_objet = $value;
					break;
				}
			}
		}
	}
	
	// Nettoyer de l'objet trouvé, puis rechercher dans la pile les autres
	foreach ($params as $name => $value)
	{
		// Supprimer si c'est l'objet qu'on a trouvé
		if ($name === 'id_'.$objet)
			unset($params[$name]);
			
		// Si aucune valeur explicite n'est donnée, tenter de reprendre dans la
		// pile
		else if (!is_string($value))
		{
			if ($index = array_search($name, $GLOBALS['context_query'], true))
				$params[$name] = $args[$index];
			if (!$params[$name] || !strlen($params[$name]))
				unset($params[$name]);
		}
	}
		
	// Remettre l'objet et le type de point
	if (isset($objet) && isset($id_objet) && is_string($objet) && strlen($objet))
	{
		$params['objet'] = $objet;
		$params['id_objet'] = $id_objet;
	}
	if (isset($type_point) && is_string($type_point) && strlen($type_point))
		$params['type_point'] = $type_point;
	
	return $params;
}


/*
 * Balises statiques
 */

// Balise URL_FICHIER : recherche d'un fichier par find_in_path.
function balise_URL_FICHIER($p)
{
	// Récupérer l'argument
	$filePath = interprete_argument_balise(1, $p);
	
	// Retour
	if (!$filePath)
		$p->code = "";
	else
		$p->code = "find_in_path(".$filePath.")";
	$p->interdire_scripts = false;
	return $p;
}

// Balise URL_FICHIER_DEF : recherche par find_in_path avec un dossier alternatif
function balise_URL_FICHIER_DEF($p)
{
	// Récupérer l'argument
	$filePath = interprete_argument_balise(1, $p);
	$folder = interprete_argument_balise(2, $p);
	
	// Retour
	if (!$filePath)
		$p->code = "";
	else
		$p->code = "_gmap_find_in_path(".$filePath.", ".$folder.")";
	$p->interdire_scripts = false;
	return $p;
}

// Balise GEOCAPABILITY : test des capacités d'une implémentation de carte
function balise_GEOCAPABILITY($p)
{
	// Récupérer l'argument
	$capability = interprete_argument_balise(1, $p);
	
	// Retour
	if (!$capability)
		$p->code = "";
	else
		$p->code = "gmap_teste_capability(".$capability.")";
	$p->interdire_scripts = false;
	return $p;
}

?>
