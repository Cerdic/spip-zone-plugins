<?php
/*
 * Plugin GMap
 * G�olocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Outils pour la gestion des balises et balises statiques (les plus simples, pour
 * les balises plus compliqu�es au niveau des arguments, le mod�le des balises
 * dynamiques de SPIP est plus appropri� car il prend en charge le d�codage des
 * param�tres.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_geoloc');


// M�canisme d'allocation automatique des num�ros de carte
$GLOBALS['currentMapID'] = 1;
$GLOBALS['nextMapID'] = 1;


/* Outils pour g�rer facilement les balises dynamiques dans le contexte de GMap
 * Les besoins sont un peu toujours les m�mes : on prend du contexte ou des
 * param�tres un id d'objet g�olocalisable et les balises acceptent d'autres param�tres
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

// Au d�part : lancer la balise dynamique
function _gmap_calculer_balise($p, $nom)
{
	return calculer_balise_dynamique(
			$p,		//  le n�ud AST pour la balise
			$nom,	//  le nom de la balise
			$GLOBALS['context_query']); //  les �l�ments utilisables de l'environnement	
}

// D�composition d'un param�tre en paire clef/valeur, selon qu'il y a ou non
// un signe '=' dans la chaine...
function _gmap_split_param($param)
{
	$key = null;
	$value = null;
	if (($pos = strpos($param, "=")) !== false)
	{
		$key = substr($param, 0, $pos);
		$value = trim(substr($param, $pos+1));
		if (!$value || !strlen($value)) // cas de l'appel depuis les mod�les o� l'on a '$key='
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

// Dans la partie statique, d�coder les param�tres
// Renvoie un tableau de param�tres nomm�s contenant :
// - toujours "objet" et "id_objet"
// - "type_point", une chaine vide s'il n'est pas d�fini
// - tous les autres param�tres pass�s � la balise
function _gmap_calculer_balise_params($args, $bStrictParams = false)
{
	// Commencer par d�coder le param�tres explicites, donc ceux qui sont apr�s ce
	// qui a �t� demand� dans la pile
	$params = array();
	for ($index = count($GLOBALS['context_query']); $index < count($args); $index++)
	{
		list($key, $value) = _gmap_split_param($args[$index]);
		if (!$key)
			continue;
		$params[$key] = $value;
	}
	
	// Si une requ�te est pr�cis�e, d�sactiver la recherche d'un point de 
	// r�f�rence � tout prix : la personne qui a donn� la requ�te doit 
	// savoir quels param�tres il faut passer
	if (isset($params['query']))
		$bStrictParams = true;
	
	// Inialiser les param�tres implicites
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
	
	// Traiter le cas de "objet" dont peut d�pendre le type d'objet choisit
	if (isset($params["objet"]))
	{
		if (is_string($params["objet"]))
			$objet = $params["objet"];
		unset($params["objet"]);
	}
	
	// Finalement, traiter le cas de id_objet qui est prioritaire, mais
	// seulement si $objet est d�j� d�fini
	if (isset($params["id_objet"]))
	{
		if ($objet && is_string($params["id_objet"]))
			$id_objet = $params["id_objet"];
		unset($params["id_objet"]);
	}
	
	// L�, on a nettoy� les param�tres pass�s manuellement ($params) de "objet", "id_objet" et
	// "type_point", si on n'a trouv� aucun objet, on va essayer d'en trouv� un dans la pile
	
	// Si on n'a pas de id_objet, mais un objet d�sign�, le chercher
	if (!$id_objet && is_string($objet))
	{
		$idName = 'id_'.$objet;
		if (isset($params[$idName]) && is_string($params[$idName]))
			$id_objet = $params[$idName];
		else if ($index = array_search($idName, $GLOBALS['context_query'], true))
			$id_objet = $args[$index];
	}
	
	// Si on n'a pas de id_objet, parcourir les param�tres explicites pour voir si
	// un type d'objet est d�sign� (c'est le cas le plus courant : on a mis dans les
	// param�tre {id_rubrique} ou {id_rubrique=XX})
	if (!$id_objet)
	{
		// Rechercher le premier param�tre qui correspond � un des param�tres implicites
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
	
	// Si on n'a toujours rien, parcourir les param�tres implicites dans l'ordre
	// de pr�f�rence
	// Ici, il serait mieux de prendre la boucle la plus proche, mais je ne sais pas
	// faire �a dans une balise dynamique : il faudrait d�coder les codes PHP renvoy�s
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
	
	// Nettoyer de l'objet trouv�, puis rechercher dans la pile les autres
	foreach ($params as $name => $value)
	{
		// Supprimer si c'est l'objet qu'on a trouv�
		if ($name === 'id_'.$objet)
			unset($params[$name]);
			
		// Si aucune valeur explicite n'est donn�e, tenter de reprendre dans la
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
	// R�cup�rer l'argument
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
	// R�cup�rer l'argument
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

// Balise GEOCAPABILITY : test des capacit�s d'une impl�mentation de carte
function balise_GEOCAPABILITY($p)
{
	// R�cup�rer l'argument
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
