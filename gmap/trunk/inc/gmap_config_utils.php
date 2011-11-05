<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Fonctions d'accès aux paramètres
 *
 */

include_spip('inc/meta');
include_spip('inc/gmap_db_utils');

// Obtenir la liste des APIs
function gmap_apis_connues()
{
	$apis = array(
		'gma2' => array( 'name' => _T('gmap:gis_api_google_maps_2'), 'explic' => _T('gmap:gis_api_google_maps_2_desc')),
		'gma3' => array( 'name' => _T('gmap:gis_api_google_maps_3'), 'explic' => _T('gmap:gis_api_google_maps_3_desc')) );
	return pipeline('gmap_implementations', $apis);
}

// Test des capacités d'une implémentation de carte
function gmap_capability($capability)
{
	// Spécificités de l'API
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$capabilities = charger_fonction("capabilities", "mapimpl/".$api."/public", true);
	if (!$capabilities)
		return false;
	
	// Renvoyer le test
	return $capabilities($capability);
}

// Vérifier qu'une valeur est définie
function gmap_config_existe($bloc, $nom)
{
	$pack = @unserialize($GLOBALS['meta'][$bloc]);
	return (($pack == NULL) || ($pack[$nom] == NULL)) ? FALSE : TRUE;
}

// Lecture d'une valeur dans les paramètres
function gmap_lire_config($bloc, $nom, $defVal = NULL)
{
	$pack = @unserialize($GLOBALS['meta'][$bloc]);
	if (($pack == NULL) || ($pack[$nom] == NULL))
		return $defVal;
	return $pack[$nom];
}

// Écriture d'une valeur dans les paramètres
function gmap_ecrire_config($bloc, $nom, $valeur)
{
	$pack = @unserialize($GLOBALS['meta'][$bloc]);
	if ($pack == NULL)
		$pack = array();
	$pack[$nom] = $valeur;
	ecrire_meta($bloc, serialize($pack));
	return TRUE;
}

// Initialisation (ecriture si n'existe pas déjà)
function gmap_init_config($bloc, $nom, $valeur)
{
	if (!gmap_config_existe($bloc, $nom))
		gmap_ecrire_config($bloc, $nom, $valeur);
}

// Teste si le plugin est actif (clef google api définie)
function gmap_est_actif()
{
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$test_actif = charger_fonction("test_actif", "mapimpl/".$api."/public", true);
	if (!$test_actif)
		return false;
	return $test_actif();
}

// Teste si un élément SPIP peut-être géolocalisé
function gmap_est_geolocalisable($objet, $id_objet)
{
	// Vérifications de sécurité
	if (!strlen($objet) || !strlen($id_objet))
		return FALSE;
		
	// Vérifier par rapport à la configuration
	$prop = "type_".$objet."s";
	$config = gmap_lire_config('gmap_objets_geo', $prop, "non");
	if ($config !== "oui")
		return FALSE;
	
	// Pour tout sauf les mot-clefs et les auteurs, vérifier que c'est dans un secteur
	// géolocalisable
	$tout_le_site = gmap_lire_config('gmap_objets_geo', 'tout_le_site', "oui");
	if (($tout_le_site === "non") && ($objet !== "auteur") && ($objet !== "mot"))
	{
		// Lire la liste des rubriques
		if (!($rubs = gmap_lire_config('gmap_objets_geo', 'liste', NULL)))
			return FALSE;
	
		// Voir si l'objet est dans la liste ou a un ancêtre dans la liste
		if (!_gmap_est_liste_geolocalisable($objet, $id_objet, $rubs))
			return FALSE;
	}
	
	return TRUE;
}
function _gmap_est_liste_geolocalisable($objet, $id_objet, $rubs)
{
	// Sur les rubriques, tester si elles sont dans la liste
	if (($objet === "rubrique") && in_array($id_objet, $rubs))
		return TRUE;
	
	// Pour tout le monde, regarder sur le(s) parent(s)
	$parents = gmap_parents($objet, $id_objet);
	foreach ($parents as $parent)
	{
		if (_gmap_est_liste_geolocalisable($parent['objet'], $parent['id_objet'], $rubs))
			return TRUE;
	}
	
	return FALSE;
}

// Formatage des chaînes de résultats des actions de configuration
function gmap_ajoute_msg($result, $ajout)
{
	if (!$result)
		$result = "";
	if ($result != "")
		$result .= "\n";
	$result .= $ajout;
	return $result;
}
function gmap_encode_result($page, $result)
{
	if ($result === "")
		return $page;
	$param = urlencode($result);
	return parametre_url($page, "msg_result", $result, '&');
}
function gmap_decode_result($class = "msg_result")
{
	$msg = "";
	if ($result = _request('msg_result'))
	{
		$result = str_replace("\n", "<br/>", $result);
		$msg .= '<p class="'.$class.'">'.urldecode($result).'</p>' . "\n";
	}
	return $msg;
}

?>