<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

//
// Import des données gis / geomap
//

function configuration_import_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");
	
	// Voir si la table spip_gis existe
	$tables = sql_alltable();
	$bIsGIS = array_search("spip_gis", $tables);

	// Voir s'il y a des données GIS à importer
	$numGis = 0;
	if ($bIsGIS)
	{
		$rowset = sql_select("count(*) as count", "spip_gis");
		if ($rowset)
		{
			$row = sql_fetch($rowset);
			$numGis = $row['count'];
		}
		sql_free($rowset);
	}
	if ($numGis == 0)
		return $corps;
	else
	{
		if ($numGis == 1)
			$phrase = _T('gmap:import_source');
		else
			$phrase = str_replace('%numGis%', $numGis, _T('gmap:import_sources'));
		$corps .= '<p>'.$phrase.'</p>' . "\n";
	}
	
	// Voir ce qu'il y a dans notre propre table
	$numGmap = 0;
	$rowset = sql_select("count(*) as count", "spip_gmap_points");
	if ($rowset)
	{
		$row = sql_fetch($rowset);
		$numGmap = $row['count'];
	}
	sql_free($rowset);
	if ($numGmap > 0)
	{
		// Début du bloc d'avertissement
		$corps .= '<div class="warning_block">' . "\n";
		
		// Avertissement
		if ($numGmap == 1)
			$phrase = _T('gmap:import_destination');
		else
			$phrase = str_replace('%numGmap%', $numGmap, _T('gmap:import_destinations'));
		$corps .= '<div class="warning_title"><p class="warning">'.$phrase.'</p></div>' . "\n";
		
		// Choix sur le traitement du contenu
		$corps .= '<div class="config_group">' . "\n";
		$corps .= '<p class="texte">'._T('gmap:import_explication_choix').'</p>' . "\n";
		$corps .= '<input type="radio" name="choix_source" id="garder" value="garder" checked="checked" /><label for="garder">'._T('gmap:import_choix_garder').'</label><br />' . "\n";
		$corps .= '<input type="radio" name="choix_source" id="effacer" value="effacer" /><label for="effacer">'._T('gmap:import_choix_effacer').'</label><br />' . "\n";
		$corps .= '<input type="radio" name="choix_source" id="merger" value="merger" /><label for="merger">'._T('gmap:import_choix_merger').'</label>' . "\n";
		$corps .= '</div>' . "\n";
		
		// Fin du bloc d'avertissement
		$corps .= '</div>' . "\n";
	}
	
	return gmap_formulaire_ajax('config_bloc_gmap', 'import', 'configurer_gmap_import', $corps,
		find_in_path('images/logo-config-import.png'),
		_T('gmap:configuration_import'), _T('gmap:import_button'));
}

?>
