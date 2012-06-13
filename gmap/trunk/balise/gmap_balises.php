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

// DEPRECATED
function _gmap_objet_englobant($p)
{
	$ret = array();

	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
    $ret['id_objet'] = champ_sql($_id_objet, $p);
    $ret['objet'] = $p->boucles[$p->id_boucle]->id_table;
    $ret['objet_type'] = objet_type($ret['objet']);
	
	return $ret;
}

function _gmap_objets_contexte($p) {

	$contexte = array();
	$contexte['objet'] = false;
	$contexte['id_objet'] = false;
	
	$idb = $p->id_boucle;
	while (isset($p->boucles[$idb]) && !$contexte['id_objet']) {
		$objet = $p->boucles[$p->id_boucle]->id_table;
		$_id_objet = $p->boucles[$p->id_boucle]->primary;
		if ($objet !== 'geopoint') {
			$contexte['objet'] = $objet;
			$contexte['id_objet'] = champ_sql($_id_objet, $p);
		}
		$idb = $boucles[$idb]->id_parent;
	}
	
	return $contexte;
}

// Au départ : lancer la balise dynamique
function _gmap_calculer_balise($p, $nom)
{
	// Calculer les paramètres dont on a besoin
	$args = array('objet', 'id_objet', 'id_geopoint', 'type_point');
	$contexte = _gmap_objets_contexte($p);
	$params = array($contexte['objet'], $contexte['id_objet']);
	
	return calculer_balise_dynamique(
			$p,			// le nœud AST pour la balise
			$nom,		// le nom de la balise
			$args,		// les éléments utilisables de l'environnement	
			$params);	// des paramètres supplémentaires passés
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
// - toujours "objet", "id_objet" et "id_geopoint"
// - "type_point", une chaine vide s'il n'est pas défini
// - tous les autres paramètres passés à la balise
function _gmap_calculer_balise_params($args, $bStrictParams = false)
{
	// Redécomposer les arguments
	$dynargs = array_slice($args, 0, 4);	// objet, id_objet, id_geopoint, type_point
	$contexte = array_slice($args, 4, 2);	// objet, id_objet
	$options = array_slice($args, 6);		// les autres paramètres de la balise
	
	// Commencer par décoder le paramètres explicites, donc ceux qui sont après ce
	// qui a été demandé dans la pile
	$params = array();
	for ($index = 0; $index < count($options); $index++)
	{
		list($key, $value) = _gmap_split_param($options[$index]);
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
	$id_geopoint = null;
	$type_point = null;
	
	// Traiter le cas "type_point" en premier, il simplifiera la suite
	if (isset($params["type_point"])) {
	
		if ($params["type_point"] === true)
			$type_point = $dynargs[3];
		else
			$type_point = $params["type_point"];
			
		unset($params["type_point"]);
	}
	
	// Id_geopoint
	if (isset($params["id_geopoint"])) {
	
		if ($params["id_geopoint"] === true)
			$id_geopoint = $dynargs[2];
		else
			$id_geopoint = $params["id_geopoint"];
			
		unset($params["id_geopoint"]);
	}
	
	// Traiter le cas de "objet" dont peut dépendre le type d'objet choisit
	if (isset($params["objet"])) {
	
		if (is_string($params["objet"]))
			$objet = $params["objet"];
			
		unset($params["objet"]);
	}
	
	// Finalement, traiter le cas de id_objet qui est prioritaire, mais
	// seulement si $objet est déjà défini
	if (isset($params["id_objet"]))	{
	
		if ($objet && is_string($params["id_objet"]))
			$id_objet = $params["id_objet"];
			
		unset($params["id_objet"]);
	}
	
	// Chercher si on a un id_* en paramètre, s'il y en a un, il faut qu'il corresponde
	// à l'objet de la boucle englobante (en gros, il ne sert à rien...)
	if (!$id_objet) {
		// Rechercher le premier paramètre qui correspond à un des paramètres implicites
		foreach ($params as $key => $value) {
		
			if (($key === "objet") || ($key === "id_objet") || ($key === "id_geopoint") || ($key === "type_point"))
				continue;
			if (preg_match("/^id_([a-z]*)$/i", $key, $matches))
			{
				if (($value === true) && ($contexte[0] === $matches[1]))
					$id_objet = $contexte[1];
				else
					$id_objet = $value;
				break;
			}
		}
	}
	
	// Si le contexte dynamique contient explicitement objet et id_objet, les utiliser
	if (!$objet || !$id_objet) {
		if ($dynargs[0] && strlen($dynargs[0]) && $dynargs[1]) {
			$objet = $dynargs[0];
			$id_objet = $dynargs[1];
		}
	}
	
	// Sinon prendre l'objet de la boucle englobante, passé dans le contexte
	if ((!$objet || !$id_objet) && ($bStrictParams !== true)) {
		if ($contexte[0] && strlen($contexte[0]) && $contexte[1]) {
			$objet = $contexte[0];
			$id_objet = $contexte[1];
		}
	}
	
	// Nettoyer de l'objet trouvé
	if (isset($params['id_'.$objet]))
		unset($params['id_'.$objet]);
		
	// Remettre les objets récupérés
	if (isset($objet) && isset($id_objet) && is_string($objet) && strlen($objet))
	{
		$params['objet'] = $objet;
		$params['id_objet'] = $id_objet;
	}
	if (isset($type_point) && is_string($type_point) && strlen($type_point))
		$params['type_point'] = $type_point;
	if (isset($id_geopoint))
		$params['id_geopoint'] = $id_geopoint;
	
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
