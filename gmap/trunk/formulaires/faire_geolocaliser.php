<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Modification de la g�olocalisation d'un objet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction de traitement du formulaire
function formulaires_faire_geolocaliser_dist()
{
	// R�cup�rer les param�tres
	$objet = _request('geoobject');
	$id_objet = _request('geoobject_id');

	// Traiter les requ�tes du formulaire
	$opResultString = "";
	
	// R�cup�rer l'info sur les marqueurs
	$count = _request('markers_count');
	$active = _request('active_marker');
	$ids = _request('marker_id');
	$states = _request('marker_state');
	$lats = _request('marker_lat');
	$longs = _request('marker_long');
	$zooms = _request('marker_zoom');
	$types = _request('marker_type');
	
	// V�rification des tableaux
	if ($count <= 0)
	{
		spip_log("Donn�es de formulaire incoh�rentes dans formulaires_faire_geolocaliser_dist (compteur = 0)", "gmap");
		$opResultString = _T('gmap:invalid_form_data');
		return $opResultString;
	}
	$countDel = 0;
	foreach ($states as $idx=>$state)
		if ($state === "deleted")
			$countDel++;
	$chkCount = $count + 1;
	if ((count($ids) != $chkCount) || (count($states) != $chkCount) || (count($types) != ($chkCount-$countDel)) ||
		(count($lats) != ($chkCount-$countDel)) || (count($longs) != ($chkCount-$countDel)) || (count($zooms) != ($chkCount-$countDel)))
	{
		spip_log("Donn�es de formulaire incoh�rentes dans formulaires_faire_geolocaliser_dist", "gmap");
		if (count($ids) != $chkCount)
			spip_log("-> ".count($ids)." �l�ments dans marker_id pour ".$chkCount." marqueurs attendus", "gmap");
		if (count($states) != $chkCount)
			spip_log("-> ".count($states)." �l�ments dans marker_state pour ".$chkCount." marqueurs attendus", "gmap");
		if (count($types) != $chkCount-$countDel)
			spip_log("-> ".count($types)." �l�ments dans marker_type pour ".($chkCount-$countDel)." marqueurs attendus", "gmap");
		if (count($lats) != $chkCount-$countDel)
			spip_log("-> ".count($lats)." �l�ments dans marker_lat pour ".($chkCount-$countDel)." marqueurs attendus", "gmap");
		if (count($longs) != $chkCount-$countDel)
			spip_log("-> ".count($longs)." �l�ments dans marker_long pour ".($chkCount-$countDel)." marqueurs attendus", "gmap");
		if (count($zooms) != $chkCount-$countDel)
			spip_log("-> ".count($zooms)." �l�ments dans marker_zoom pour ".($chkCount-$countDel)." marqueurs attendus", "gmap");
		$opResultString = _T('gmap:invalid_form_data');
		return $opResultString;
	}

	// Parcours des points
	$indexVisible = 0;
	$countAdded = 0;
	$countDeleted = 0;
	$countUpdated = 0;
	for ($index = 0; $index < $count; $index++)
	{
		if ($states[$index] == 'created')
		{
			$idnew = gmap_add_point($objet, $id_objet, $lats[$indexVisible], $longs[$indexVisible], $zooms[$indexVisible], $types[$indexVisible]);
			$indexVisible++;
			if ($idnew != 0)
				$countAdded++;
		}
		else if ($states[$index] == 'deleted')
		{
			if (gmap_delete_point($objet, $id_objet, $ids[$index]))
				$countDeleted++;
		}
		else
		{
			if (gmap_update_point($objet, $id_objet, $ids[$index], $lats[$indexVisible], $longs[$indexVisible], $zooms[$indexVisible], $types[$indexVisible]))
				$countUpdated++;
			$indexVisible++;
		}
	}
	
	// Formatage de la cha�ne d'information sur le r�sultat
	
	return $opResultString;
}

?>
