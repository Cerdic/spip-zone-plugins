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

// Au d�part : lancer la balise dynamique
function _gmap_calculer_balise($p, $nom)
{
	// Calculer les param�tres dont on a besoin
	$args = array('objet', 'id_objet', 'id_geopoint', 'type_point');
	$contexte = _gmap_objets_contexte($p);
	$params = array($contexte['objet'], $contexte['id_objet']);
	
	return calculer_balise_dynamique(
			$p,			// le n�ud AST pour la balise
			$nom,		// le nom de la balise
			$args,		// les �l�ments utilisables de l'environnement	
			$params);	// des param�tres suppl�mentaires pass�s
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
// - toujours "objet", "id_objet" et "id_geopoint"
// - "type_point", une chaine vide s'il n'est pas d�fini
// - tous les autres param�tres pass�s � la balise
function _gmap_calculer_balise_params($args, $bStrictParams = false)
{
	// Red�composer les arguments
	$dynargs = array_slice($args, 0, 4);	// objet, id_objet, id_geopoint, type_point
	$contexte = array_slice($args, 4, 2);	// objet, id_objet
	$options = array_slice($args, 6);		// les autres param�tres de la balise
	
	// Commencer par d�coder le param�tres explicites, donc ceux qui sont apr�s ce
	// qui a �t� demand� dans la pile
	$params = array();
	for ($index = 0; $index < count($options); $index++)
	{
		list($key, $value) = _gmap_split_param($options[$index]);
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
	
	// Traiter le cas de "objet" dont peut d�pendre le type d'objet choisit
	if (isset($params["objet"])) {
	
		if (is_string($params["objet"]))
			$objet = $params["objet"];
			
		unset($params["objet"]);
	}
	
	// Finalement, traiter le cas de id_objet qui est prioritaire, mais
	// seulement si $objet est d�j� d�fini
	if (isset($params["id_objet"]))	{
	
		if ($objet && is_string($params["id_objet"]))
			$id_objet = $params["id_objet"];
			
		unset($params["id_objet"]);
	}
	
	// Chercher si on a un id_* en param�tre, s'il y en a un, il faut qu'il corresponde
	// � l'objet de la boucle englobante (en gros, il ne sert � rien...)
	if (!$id_objet) {
		// Rechercher le premier param�tre qui correspond � un des param�tres implicites
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
	
	// Sinon prendre l'objet de la boucle englobante, pass� dans le contexte
	if ((!$objet || !$id_objet) && ($bStrictParams !== true)) {
		if ($contexte[0] && strlen($contexte[0]) && $contexte[1]) {
			$objet = $contexte[0];
			$id_objet = $contexte[1];
		}
	}
	
	// Nettoyer de l'objet trouv�
	if (isset($params['id_'.$objet]))
		unset($params['id_'.$objet]);
		
	// Remettre les objets r�cup�r�s
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
