<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Requête sur les voisins
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_geoloc_outil');
include_spip('inc/gmap_geoloc');

// Helpers des requêtes
function _geoloc_requete_siblings_format_distdate($distdate)
{
	$seconds = $distdate;
	if ($distdate > 60)
	{
		$minutes = intval($seconds/60);
		$seconds = $seconds%60;
	}
	if ($minutes > 60)
	{
		$hours = intval($minutes/60);
		$minutes = $minutes%60;
	}
	if ($hours > 24)
	{
		$days = intval($hours/24);
		$hours = $hours%24;
	}
	
	$format = '';
	if (isset($days) && isset($hours))
		$format = _T('gmap:format_distdate_dh');
	else if (isset($hours) && isset($minutes))
		$format = _T('gmap:format_distdate_hm');
	else if (isset($minutes) && isset($seconds))
		$format = _T('gmap:format_distdate_ms');
	else if (isset($seconds))
		$format = _T('gmap:format_distdate_s');
	if (!strlen($format))
		return '';

	return str_replace(array('%day%','%hour%','%minute%', '%second%'), array($days,$hours,$minutes,$seconds), $format);
}
function _geoloc_requete_siblings_interval_to_seconds($timeInterval, $timeIntervalUnit)
{
	$time = intval($timeInterval)*3600;
	if ($timeIntervalUnit != 'heure')
	{
		$time *= 24;
		if ($timeIntervalUnit == 'semaine')
			$time *= 7;
		else if ($timeIntervalUnit == 'mois')
			$time *= 30;
	}
	return $time;
}
function _geoloc_requete_siblings_clause_bounds($table, $bounds)
{
	$where = '(('.$table.'.latitude >= '.$bounds['min_lat'].') AND ('.$table.'.latitude <= '.$bounds['max_lat'].')) AND ';
	if ($bounds['min_lng'] > $bounds['max_lng'])
		$where .= '(('.$table.'.longitude >= '.$bounds['min_lng'].') OR ('.$table.'.longitude <= '.$bounds['max_lng'].'))';
	else
		$where .= '(('.$table.'.longitude >= '.$bounds['min_lng'].') AND ('.$table.'.longitude <= '.$bounds['max_lng'].'))';
	return $where;
}

// Requête sur les articles voisins
function _geoloc_requete_siblings_get_articles($id_article, $limit, $bSameParent, $interval, $timeInterval, $timeIntervalUnit, $limiteViewport, $bounds)
{
    // Initialisation du retour
    $markers = null;
	
	// Récupérer les infos sur l'article
	if (!($row = sql_fetsel("id_rubrique, date", "spip_articles", "id_article = $id_article")))
		return null;

	// Requête pour récupérer les articles géoréférencés et espacés de moins d'une semaine
	$where = array("articles.id_article<>" . $id_article);
	if ($bSameParent === true)
		$where[] = "articles.id_rubrique=" . $row['id_rubrique'];
	if ($interval && ($time == _geoloc_requete_siblings_interval_to_seconds($timeInterval, $timeIntervalUnit)))
		$where[] = "ABS(TIMESTAMPDIFF(SECOND,'" . $row['date'] . "', articles.date)) < ".$time;
	if ($limiteViewport && $bounds)
		$where[] = _geoloc_requete_siblings_clause_bounds('points', $bounds);
	$rowset = sql_select(
		array(
			"articles.id_article AS id", "articles.titre AS titre", "articles.date AS date",
			"points.latitude AS coord_lat", "points.longitude AS coord_long", "points.zoom AS zoom", "points.id_point AS id_point",
			"types.nom AS type",
			"ABS(TIMESTAMPDIFF(SECOND,'" . $row['date'] . "', articles.date)) AS distdate"),
		"spip_articles AS articles".
		" JOIN spip_gmap_points_liens AS liens ON (articles.id_article = liens.id_objet AND liens.objet = 'article')".
		" JOIN spip_gmap_points AS points ON points.id_point = liens.id_point".
		" JOIN spip_gmap_types AS types ON points.id_type_point = types.id_type_point",
		$where,
		"", "distdate ASC", $limit);
	// L'alias sur les noms des tables est nécessaire parce que spip ne peut pas prendre en 
	// charge tous les cas dans la transposition des nom de tables : un nom de table précédé
	// d'une parenthèse n'est pas transposé (cf. _SQL_PREFIXE_TABLE dans ecrire/req/mysql.php).
	while ($row = sql_fetch($rowset))
	{
		if ($markers == NULL)
		{
			$markers = array();
			$keys = array();
		}
		$keys[] = $row['date'];
		$markers[] = array(
			'id_point'=>$row['id_point'],
			'objet'=>'article',
			'id_objet'=>$row['id'],
			'title'=>$row['titre'],
			'latitude'=>$row['coord_lat'],
			'longitude'=>$row['coord_long'],
			'zoom'=>$row['zoom'],
			'type_point'=>$row['type'],
			'distance'=>_geoloc_requete_siblings_format_distdate($row['distdate']),
			'html'=>gmap_get_object_info_contents(array(
						'objet'=>'article',
						'id_objet'=>$row['id'],
						'id_article'=>$row['id'],
						'type_point'=>$row['type'],
						'id_point'=>$row['id_point']))
			);
	}
	if ($keys && $markers)
		array_multisort($keys, SORT_ASC, $markers);
    
    return $markers;
}

// Requête sur les documents voisins
function _geoloc_requete_siblings_get_documents($id_document, $limit, $bSameParent, $interval, $timeInterval, $timeIntervalUnit, $limiteViewport, $bounds)
{
    // Initialisation du retour
    $markers = null;
	
	// Récupérer les infos du documents
	if (!($row = sql_fetsel("spip_documents_liens.objet AS objet, spip_documents_liens.id_objet AS id_objet, spip_documents.date AS date",
							"spip_documents_liens JOIN spip_documents ON spip_documents_liens.id_document = spip_documents.id_document",
							"spip_documents_liens.id_document=$id_document")))
		return null;

	// Requête pour récupérer les documents géoréférencés et espacés de moins de deux heures
	$where = array("spip_documents_liens.id_document<>" . $id_document);
	if ($bSameParent === true)
		$where[] = "spip_documents_liens.objet='".$row['objet']."' AND spip_documents_liens.id_objet=" . $row['id_objet'];
	if ($interval && ($time = _geoloc_requete_siblings_interval_to_seconds($timeInterval, $timeIntervalUnit)))
		$where[] = "ABS(TIMESTAMPDIFF(SECOND,'" . $row['date'] . "', spip_documents.date)) < ".$time;
	if ($limiteViewport && $bounds)
		$where[] = _geoloc_requete_siblings_clause_bounds('spip_gmap_points', $bounds);
	$rowset = sql_select(
		array(
			"spip_documents_liens.id_document AS id", "spip_documents.titre AS titre", "spip_documents.fichier AS fichier", "spip_documents.date AS date",
			"spip_gmap_points.latitude AS coord_lat", "spip_gmap_points.longitude AS coord_long", "spip_gmap_points.zoom AS zoom", "spip_gmap_points.id_point AS id_point",
			"spip_gmap_types.nom AS type",
			"ABS(TIMESTAMPDIFF(SECOND,'" . $row['date'] . "', spip_documents.date)) AS distdate"),
		"spip_documents_liens JOIN spip_documents ON spip_documents_liens.id_document = spip_documents.id_document JOIN spip_gmap_points_liens ON (spip_documents_liens.id_document = spip_gmap_points_liens.id_objet AND spip_gmap_points_liens.objet = 'document') JOIN spip_gmap_points ON spip_gmap_points.id_point = spip_gmap_points_liens.id_point JOIN spip_gmap_types ON spip_gmap_points.id_type_point = spip_gmap_types.id_type_point",
		$where,
		"",	"distdate ASC", $limit);
//spip_log("SELECT spip_documents_liens.id_document AS id, spip_documents.titre AS titre, spip_documents.fichier AS fichier, spip_documents.date AS date, spip_gmap_points.latitude AS coord_lat, spip_gmap_points.longitude AS coord_long, spip_gmap_points.zoom AS zoom, spip_gmap_points.id_point AS id_point, spip_gmap_types.nom AS type, ABS(TIMESTAMPDIFF(SECOND,'" . $row['date'] . "', spip_documents.date)) AS distdate", "fabdbg");
//spip_log(" FROM spip_documents_liens JOIN spip_documents ON spip_documents_liens.id_document = spip_documents.id_document JOIN spip_gmap_points_liens ON (spip_documents_liens.id_document = spip_gmap_points_liens.id_objet AND spip_gmap_points_liens.objet = 'document') JOIN spip_gmap_points ON spip_gmap_points.id_point = spip_gmap_points_liens.id_point JOIN spip_gmap_types ON spip_gmap_points.id_type_point = spip_gmap_types.id_type_point", "fabdbg");
//spip_log(" WHERE ".print_r($where, true), "fabdbg");
//spip_log(" ORDER BY distdate ASC", "fabdbg");
//spip_log(" LIMIT $limit", "fabdbg");
	while ($row = sql_fetch($rowset))
	{
		if ($markers == NULL)
		{
			$markers = array();
			$keys = array();
		}
		$keys[] = $row['date'];
		$markers[] = array(
			'id_point'=>$row['id_point'],
			'objet'=>'document',
			'id_objet'=>$row['id'],
			'title'=>$row['titre'] ? $row['titre']." (".$row['fichier'].")" : $row['fichier'],
			'latitude'=>$row['coord_lat'],
			'longitude'=>$row['coord_long'],
			'zoom'=>$row['zoom'],
			'type_point'=>$row['type'],
			'distance'=>_geoloc_requete_siblings_format_distdate($row['distdate']),
			'html'=>gmap_get_object_info_contents(array(
						'objet'=>'document',
						'id_objet'=>$row['id'],
						'id_document'=>$row['id'],
						'type_point'=>$row['type'],
						'id_point'=>$row['id_point']))
		   );
	}
	if ($keys && $markers)
		array_multisort($keys, SORT_ASC, $markers);
    
    return $markers;
}

// helpers
function _geoloc_requete_siblings_lire_config($request, $config, $objet, $default)
{
	$value = _request($request);
	if (!isset($value))
	{
		$value = gmap_lire_config('gmap_geoloc_params', $config.'_'.$objet, "notset");
		if ($value === "notset")
			$value = gmap_lire_config('gmap_edit_params', $config, $default);
	}
	// Réécrire si la valeur est venue de la requête
	else
		gmap_ecrire_config('gmap_geoloc_params', $config.'_'.$objet, $value);
	return $value;
}

function exec_geoloc_requete_siblings_dist($class = null)
{
	$out = '';
	$out .= gmap_commencer_page_geojson();

	// Récupérer les paramètres, obligatoires ou optionnels, de la requête
	$objet = _request('objet');
	$id_objet = _request('id_objet');
	if ($objet && $id_objet)
	{
		$limiteViewport = (_geoloc_requete_siblings_lire_config('limite_vue', 'siblings_geo_interval', $objet, "non") === 'oui') ? true : false;
		if ($limiteViewport)
			$bounds = array(
				'min_lat' => _request('bounds_min_lat'),
				'min_lng' => _request('bounds_min_lng'),
				'max_lat' => _request('bounds_max_lat'),
				'max_lng' => _request('bounds_max_lng'));
		$interval = (_geoloc_requete_siblings_lire_config('interval_temps', 'siblings_time_interval', $objet, "non") === 'oui') ? true : false;
		$timeInterval = _geoloc_requete_siblings_lire_config('interval', 'siblings_interval', $objet, (($objet == 'document') ? "2" : "7"));
		$timeIntervalUnit = _geoloc_requete_siblings_lire_config('unite_interval', 'siblings_unite_interval', $objet, (($objet == 'document') ? "heure" : "jour"));
		$meme_parent = (_geoloc_requete_siblings_lire_config('meme_parent', 'siblings_same_parent', $objet, "oui") === 'oui') ? true : false;
		$limite = _geoloc_requete_siblings_lire_config('limite', 'siblings_limit', $objet, "5");
		
		$markers = null;
		if ($objet == 'article')
			$markers = _geoloc_requete_siblings_get_articles($id_objet, $limite, $meme_parent, $interval, $timeInterval, $timeIntervalUnit, $limiteViewport, $bounds);
		else if ($objet == 'document')
			$markers = _geoloc_requete_siblings_get_documents($id_objet, $limite, $meme_parent, $interval, $timeInterval, $timeIntervalUnit, $limiteViewport, $bounds);
			
		if ($markers && (count($markers) > 0))
		{
			foreach ($markers as $index => $marker)
				$out .= gmap_ajoute_point_geojson($marker, ($index == 0) ? true : false);
		}
	}
	
	$out .= gmap_fin_page_geojson();
	echo $out;
}

?>