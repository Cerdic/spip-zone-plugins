<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Import des données gis / geomap
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_db_utils');

//
// Import des données gis / geomap
//

function import_gis1($sourceAction, $result)
{
	// Rechercher dans la table spip_gis
	$rowset = sql_select("*", "spip_gis");
	if (!$rowset)
	{
		$result = gmap_ajoute_msg($result, _T('gmap:import_erreur_gis1'));
		return array($result, 0);
	}
	
	$count = 0;
	while ($row = sql_fetch($rowset))
	{
		// Décoder le type d'objet
		$objet = NULL;
		$id = 0;
		if ($row['id_rubrique'] != 0)
		{
			$objet = "rubrique";
			$id = $row['id_rubrique'];
		}
		else if ($row['id_article'] != 0)
		{
			$objet = "article";
			$id = $row['id_article'];
		}
		else if ($row['id_document'] != 0)
		{
			$objet = "document";
			$id = $row['id_document'];
		}
		
		// Ajouter le point
		if (($sourceAction != 'merger') ||
			!sql_countsel('spip_gmap_points_liens', "objet='".$objet."' AND id_objet=".$id))
			$id_point = gmap_add_point($objet, $id, $row['lat'], $row['lonx'], $row['zoom']);
		if ($id_point != 0)
			$count++;
	}
	sql_free($rowset);
	
	return array($result, $count);
}

function import_gis2($sourceAction, $result)
{
	// Rechercher dans la table spip_gis
	$rowset = sql_select("*", "spip_gis JOIN spip_gis_liens ON spip_gis.id_gis = spip_gis_liens.id_gis");
	if (!$rowset)
	{
		$result = gmap_ajoute_msg($result, _T('gmap:import_erreur_gis1'));
		return array($result, 0);
	}
	
	$count = 0;
	while ($row = sql_fetch($rowset))
	{
		// Décoder le type d'objet
		$objet = $row['objet'];
		$id = intval($row['id_objet']);
		
		// Ajouter le point
		if (($sourceAction != 'merger') ||
			!sql_countsel('spip_gmap_points_liens', "objet='".$objet."' AND id_objet=".$id))
			$id_point = gmap_add_point($objet, $id, $row['lat'], $row['lon'], $row['zoom']);
		if ($id_point != 0)
			$count++;
	}
	sql_free($rowset);
	
	return array($result, $count);
}

function configuration_faire_outil_import_gis_dist()
{
	$result = "";
	
	// Regarder ce qu'il faut faire de l'existant
	$sourceAction = _request('choix_source');
	if ($sourceAction == 'effacer')
	{
		sql_delete('spip_gmap_points');
		sql_delete('spip_gmap_points_liens');
		sql_delete('spip_gmap_labels');
	}
	
	// Voir si la table spip_gis existe
	$tables = sql_alltable();
	$bIsGIS = array_search("spip_gis", $tables);
	$bIsGIS2 = array_search("spip_gis_liens", $tables);
	if ($bIsGIS && $bIsGIS2)
		list($result, $count) = import_gis2($sourceAction, $result);
	else if ($bIsGIS)
		list($result, $count) = import_gis1($sourceAction, $result);
	else
		$count = 0;
	
	// Message de retour
	$msg = "";
	if ($count == 0)
		$msg = _T('gmap:import_result_none');
	else if ($count == 1)
		$msg = _T('gmap:import_result_point');
	else
		$msg = str_replace('%count%', $count, _T('gmap:import_result_points'));
	if ($msg != "")
		$result = gmap_ajoute_msg($result, $msg);
	return $result;
}

?>
