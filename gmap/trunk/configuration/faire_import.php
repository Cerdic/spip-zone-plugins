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

function configuration_faire_import_dist()
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
	
	// Rechercher dans la table spip_gis
	$rowset = sql_select("*", "spip_gis");
	if (!$rowset)
		$result = gmap_ajoute_msg($result, "Erreur : Acc&egrave;s à la base de donn&eacute;es GIS impossible");
	else
	{
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
				$id_point = gmap_add_point($objet, $id, $row['lat'], $row['lonx'], $row['zoom'], $row['type']);
			if ($id_point != 0)
				$count++;
		}
	}
	sql_free($rowset);
	
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
