<?php
/*
 * Plugin GMap
 * G�olocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2011 - licence GNU/GPL
 *
 * Fonctions d'acc�s � la base de donn�es
 *
 */

include_spip('base/abstract_sql');
 
// Ajout des types de documents KML/KMZ s'il n'y sont pas d�j�
function gmap_verif_types_documents()
{
    $rowset = sql_select("extension", "spip_types_documents", "extension='kml'");
    if (!$row = sql_fetch($rowset))
		sql_insertq("spip_types_documents", array(
				'titre' => 'Google Earth Placemark',
				'descriptif' => '',
				'extension' => 'kml',
				'mime_type' => 'application/vnd.google-earth.kml+xml',
				'inclus' => 'non',
				'upload' => 'oui',
				'maj' => 'NOW()'));
	sql_free($rowset);
    $rowset = sql_select("extension", "spip_types_documents", "extension='kmz'");
    if (!$row = sql_fetch($rowset))
		sql_insertq("spip_types_documents", array(
				'titre' => 'Google Earth Placemark',
				'descriptif' => '',
				'extension' => 'kmz',
				'mime_type' => 'application/vnd.google-earth.kmz',
				'inclus' => 'non',
				'upload' => 'oui',
				'maj' => 'NOW()'));
	sql_free($rowset);
}

// Cr�ation d'un type de marqueur
function gmap_cree_type($nom, $descriptif, $objet = "", $visible = "oui", $priorite = 2)
{
	sql_insertq("spip_gmap_types", array(
		"objet" => $objet,
    	"nom" => $nom,
    	"descriptif" => $descriptif,
		"visible" => $visible,
		"priorite" => intval($priorite)));
}
function gmap_update_type($id, $nom, $descriptif, $objet = "", $visible = "oui", $priorite = 2)
{
	sql_updateq("spip_gmap_types", array(
		"objet" => $objet,
    	"nom" => $nom,
    	"descriptif" => $descriptif,
		"visible" => $visible,
		"priorite" => $priorite),
		'id_type_point=' . intval($id));
}

// Cr�ation des types de pointeur par d�faut
function gmap_cree_types_defaut()
{
	gmap_cree_type("defaut", _T('gmap:marker_def_defaut'), "", "oui", 1);
	gmap_cree_type("centre", _T('gmap:marker_def_centre'), "", "non", 99);
	gmap_cree_type("etape", _T('gmap:marker_def_article_etape'), "article", "oui", 4);
	gmap_cree_type("prise", _T('gmap:marker_def_document_prise'), "document", "oui", 2);
	gmap_cree_type("visee", _T('gmap:marker_def_document_visee'), "document", "oui", 4);
}

// Destruction d'un type
function gmap_delete_type($id)
{
	sql_delete('spip_gmap_types', 'id_type_point=' . intval($id));
}

// R�cup�rer tous les types de points
function gmap_get_all_types()
{
	$types = array();
	/*
	SELECT spip_gmap_types.id_type_point AS id, spip_gmap_types.nom AS nom, count(*) 
	 FROM spip_gmap_types JOIN spip_gmap_points ON spip_gmap_types.id_type_point = spip_gmap_points.id_type_point
	 GROUP BY spip_gmap_types.id_type_point 
	*/
	$rowset = sql_select(
		array(
			"spip_gmap_types.id_type_point AS id",
			"spip_gmap_types.objet AS objet",
			"spip_gmap_types.nom AS nom",
			"spip_gmap_types.descriptif AS descriptif",
			"spip_gmap_types.visible AS visible",
			"spip_gmap_types.priorite AS priorite",
			"count(points.id_point) AS nb_points"),
		"spip_gmap_types".
		" LEFT JOIN spip_gmap_points AS points ON spip_gmap_types.id_type_point = points.id_type_point",
		"", "spip_gmap_types.id_type_point", "spip_gmap_types.id_type_point");
	// L'alias sur les noms des tables est n�cessaire parce que spip ne peut pas prendre en 
	// charge tous les cas dans la transposition des nom de tables : un nom de table pr�c�d�
	// d'une parenth�se n'est pas transpos� (cf. _SQL_PREFIXE_TABLE dans ecrire/req/mysql.php).
	while ($row = sql_fetch($rowset))
		$types[] = $row;
	return $types;
}

// Mettre � jour les types

// Recherche d'un type de pointeur d'apr�s son nom
function gmap_trouve_type_point($objet = "", $type = "defaut")
{
	// Corriger la cha�ne de type
	if (!$type || ($type == ""))
		$type = "defaut";
	$id_type = NULL;
		
	// Rechercher avec le nom de l'objet
	$rowsetType = sql_select("id_type_point", "spip_gmap_types", "nom = '".$type."' AND objet='".$objet."'");
	if ($rowType = sql_fetch($rowsetType))
		$id_type = $rowType['id_type_point'];
	sql_free($rowsetType);
	if ($id_type)
		return $id_type;
	
	// Recherche sans le nom de l'objet
	$rowsetType = sql_select("id_type_point", "spip_gmap_types", "nom = '".$type."' AND objet=''");
	if ($rowType = sql_fetch($rowsetType))
		$id_type = $rowType['id_type_point'];
	sql_free($rowsetType);
	if ($id_type)
		return $id_type;
		
	// Sinon renvoyer le d�faut
	if ($type != "defaut")
		return gmap_trouve_type_point($objet, "defaut");
	else
		return 0;
}

// R�cup�rer les marqueurs d'un objet
function gmap_get_types($objet)
{
	$types = array();
	$rowset = sql_select("id_type_point AS id, nom", "spip_gmap_types", "objet='".$objet."' OR objet=''");
	while ($row = sql_fetch($rowset))
		$types[] = $row;
	return $types;
}

// Ajout d'un point (sans se demander s'il existe ou pas)
function gmap_add_point($objet, $id_objet, $lat, $long, $zoom, $type = "defaut")
{
	$id = 0;

	// R�cup�rer le type de pointeur
	$id_type_point = gmap_trouve_type_point($objet, $type);
			
	// Ins�rer dans la table
	$id = sql_insertq("spip_gmap_points", array(
		"longitude" => $long,
		"latitude" => $lat,
		"zoom" => $zoom,
		"id_type_point" => $id_type_point));
		
	// Ins�rer dans la table des relations
	if ($id > 0)
	{
		sql_insertq("spip_gmap_points_liens", array(
			'id_point' => $id,
			'id_objet' => $id_objet,
			'objet' => $objet));
	}
	
	return $id;
}

// R�utilisation d'un point existant sur un autre objet
/*function gmap_reuse_point($id_point, $objet, $id_objet)
{
	$row = sql_fetsel("objet, id_objet", "spip_gmap_points_liens", "id_point=".$id_point);
	sql_insertq("spip_gmap_points_liens", array(
		'id_point' => $id_point,
		'id_objet' => $id_objet,
		'objet' => $objet));
	return true;
}*/

// Mise � jour d'un point
function gmap_update_point($objet, $id_objet, $id, $lat, $long, $zoom, $type = "defaut")
{
	// V�rifier la coh�rence de la liaison
	if (!gmap_check_point_owner($id, $objet, $id_objet))
	{
		spip_log("Tentative de modification d'un point affect� � un autre objet", "gmap");
		return FALSE;
	}
	
	// R�cup�rer le type de pointeur
	$id_type = gmap_trouve_type_point($objet, $type);
	
	// Faire la mise � jour
	$success = sql_updateq("spip_gmap_points",
		array(
			"longitude" => $long,
			"latitude" => $lat,
			"zoom" => $zoom,
			"id_type_point" => $id_type),
		"id_point=".$id);
	
	return $success;
}

// Suppression d'un point
function gmap_delete_point($objet, $id_objet, $id)
{
	// V�rifier la coh�rence de la liaison
	if (!gmap_check_point_owner($id, $objet, $id_objet))
	{
		spip_log("Tentative de modification d'un point affect� � un autre objet", "gmap");
		return FALSE;
	}
	
	// Suppression du point et des liens
	sql_delete("spip_gmap_labels", "id_point=".$id);
	sql_delete("spip_gmap_points_liens", "id_point=".$id);
	sql_delete("spip_gmap_points", "id_point=".$id);
	
	return TRUE;
}

// V�rification de la coh�rence d'un id de point par rapport � l'objet
function gmap_check_point_owner($id, $objet, $id_objet)
{
	$row = sql_fetsel("objet, id_objet", "spip_gmap_points_liens", "id_point=".$id);
	if (!$row)
		return false;
	return (($row['objet'] == $objet) && ($row['id_objet'] == $id_objet)) ? TRUE : FALSE;
}

// R�cup�rer les parents d'un objet
// Un document peut avoir plusieurs parents, c'est pourquoi il y a une liste...
// Renvoie un tableau de tableau associatif : "objet" => $objet, "id_objet" => $id_objet
function gmap_parents($objet, $id_objet)
{
	$parents = array();
	if (!$id_objet || !is_numeric($id_objet))
		return $parents;
	if ($objet === "document")
	{
		$rowset = sql_select("objet, id_objet", "spip_documents_liens", "id_document=".$id_objet);
		while ($row = sql_fetch($rowset))
		{
			$elem = array("objet"=>$row['objet'], "id_objet"=>$row['id_objet']);
			$parents[] = $elem;
		}
	}
	else if ($objet === "breve")
	{
		$rowset = sql_select("id_rubrique", "spip_breves", "id_breve=".$id_objet);
		if ($row = sql_fetch($rowset))
		{
			$elem = array("objet"=>"rubrique", "id_objet"=>$row['id_rubrique']);
			$parents[] = $elem;
		}
	}
	else if ($objet === "article")
	{
		$rowset = sql_select("id_rubrique", "spip_articles", "id_article=".$id_objet);
		if ($row = sql_fetch($rowset))
		{
			$elem = array("objet"=>"rubrique", "id_objet"=>$row['id_rubrique']);
			$parents[] = $elem;
		}
	}
	else if ($objet === "rubrique")
	{
		$rowset = sql_select("id_parent", "spip_rubriques", "id_rubrique=".$id_objet);
		if (($row = sql_fetch($rowset)) && $row['id_parent'])
		{
			$elem = array("objet"=>"rubrique", "id_objet"=>$row['id_parent']);
			$parents[] = $elem;
		}
	}
	return $parents;
}

// R�cup�rer la rubrique parente
function gmap_get_rubrique($objet, $id_objet)
{
	$parents = gmap_parents($objet, $id_objet);
	if (count($parents) !== 1) // les docs qui ont deux parents n'appartiennent pas � une rubrique
		return 0;
	if ($parents[0]['objet'] === "rubrique")
		return $parents[0]['id_objet'];
	return gmap_get_rubrique($parents[0]['objet'], $parents[0]['id_objet']);
}

// R�cup�rer les marqueurs d'un objet
function gmap_get_points($objet, $id_objet)
{
	if (!strlen($objet) || !$id_objet)
		return null;
	$points = array();
	$rowset = sql_select(
		array("points.id_point AS id", "points.longitude AS longitude", "points.latitude AS latitude", "points.zoom AS zoom", "types.nom AS type", "types.visible AS visible", "types.priorite AS priorite"),
		"spip_gmap_points_liens AS liens JOIN spip_gmap_points AS points ON liens.id_point=points.id_point JOIN spip_gmap_types AS types ON points.id_type_point = types.id_type_point",
		"liens.objet = '".$objet."' AND liens.id_objet = ".$id_objet);
	while ($row = sql_fetch($rowset))
		$points[] = $row;
	return $points;
}

// R�cup�rer un seul positionnement
function gmap_get_point($objet, $id_objet, $type = "")
{
	if (!strlen($objet) || !$id_objet)
		return null;
	$points = gmap_get_points($objet, $id_objet);
	if (!$points)
		return null;
	$thePoint = null;
	if (strlen($type))
	{
		foreach ($points as $point)
		{
			if ($point['type'] == $type)
			{
				$thePoint = $point;
				break;
			}
		}
	}
	else
	{
		$bestPriority = 99;
		foreach ($points as $point)
		{
			if ($point['visible'] !== 'oui')
				continue;
			if (!$thePoint || ($point['priorite'] < $bestPriority))
				$thePoint = $point;
		}
	}
	return $thePoint;
}

// R�cup�rer les marqueurs d'un objet
function _gmap_recurs_tree_points(&$points, $objet, $id_objet, $niveau_fils, $niveau)
{
	if ($niveau_fils < 0)
		return FALSE;
	
	// Sur l'objet lui-m�me
	$objPoints = gmap_get_points($objet, $id_objet);
	foreach ($objPoints as $row)
	{
		$row['objet'] = $objet;
		$row['id_objet'] = $id_objet;
		$row['level'] = $niveau;
		$points[] = $row;
	}
	
	// Les fils
	if ($niveau_fils > 0)
	{
		$IDs = array();
		_gmap_recurse_fils($objet, $id_objet, &$IDs, FALSE);
		foreach ($IDs as $objetFils => $idListe)
		{
			$objIds = explode(",", $idListe);
			foreach ($objIds as $idxId => $idFils)
				_gmap_recurs_tree_points($points, $objetFils, $idFils, $niveau_fils-1, $niveau+1);
		}
	}
		
	return TRUE;
}
function gmap_get_tree_points($objet, $id_objet, $niveau_fils = 0)
{
	$points = array();
	_gmap_recurs_tree_points($points, $objet, $id_objet, $niveau_fils, 0);
	return $points;
}

// Collecte des fils
// $objet, $id_objet = identification de l'objet
// $IDs = tableau associatif renseign� par la fonction :
// 	tableau associatif contenant, pour chaque type d'objet, la liste des id trouv�s, s�par�s par une virgule
function _gmap_fill_ID_tab(&$IDs, $table, $id, $where, $type, $recursive = TRUE)
{
	$rowset = sql_select($id." as id", $table, $where);
	while ($row = sql_fetch($rowset))
	{
		if (!$IDs[$type])
			$IDs[$type] = "";
		else
			$IDs[$type] .= ",";
		$IDs[$type] .= $row['id'];
		if ($recursive === TRUE)
			_gmap_recurse_fils($type, $row['id'], $IDs, TRUE);
	}
	sql_free($rowset);
}
function _gmap_recurse_fils($objet, $id_objet, &$IDs, $recursive = TRUE)
{
	// Rechercher les documents (tous objets)
	if ($objet != 'document')
		_gmap_fill_ID_tab(&$IDs, 'spip_documents_liens', 'id_document', "objet='".$objet."' AND id_objet=".$id_objet, 'document', $recursive);
	
	// Le reste est selon les objets
	// Rubrique :
	if ($objet == 'rubrique')
	{
		// Sous-rubriques
		_gmap_fill_ID_tab(&$IDs, 'spip_rubriques', 'id_rubrique', "id_parent=".$id_objet, 'rubrique', $recursive);
		
		// Articles
		_gmap_fill_ID_tab(&$IDs, 'spip_articles', 'id_article', "id_rubrique=".$id_objet, 'article', $recursive);
		
		// Mots-clefs
		_gmap_fill_ID_tab(&$IDs, 'spip_mots_rubriques', 'id_mot', "id_rubrique=".$id_objet, 'mot', $recursive);
	}

	// Article :
	else if ($objet == 'article')
	{
		// Mots-clefs
		_gmap_fill_ID_tab(&$IDs, 'spip_mots_articles', 'id_mot', "id_article=".$id_objet, 'mot', $recursive);
	}

	// Document :
	else if ($objet == 'document')
	{
		// Mots-clefs
		_gmap_fill_ID_tab(&$IDs, 'spip_mots_documents', 'id_mot', "id_document=".$id_objet, 'mot', $recursive);
	}

	// Breve :
	else if ($objet == 'breve')
	{
		// Mots-clefs
		_gmap_fill_ID_tab(&$IDs, 'spip_mots_breves', 'id_mot', "id_breve=".$id_objet, 'mot', $recursive);
	}
}

// Compte le nombre de points g�ographiques d�finis sous un objet SPIP
// $objet, $id_objet : identification de l'objet
// $visible : filtre sur le fait que le type soit marqu� visible
// $recursive : cherche aussi sous les fils
// $filtre : fltre sur le type de points : une chaine ou un tableau de chaines
// Retour : un entier qui indique le nombre de points trouv�s
function gmap_compteur($objet = '', $id_objet = 0, $visible = false, $recursive = false, $filtre = null)
{
	// Calculer la clause WHERE sur les objets
	$clauseWhere = "";
	$joinLiens = "";
	if (strlen($objet) && $id_objet)
	{
		$idWhere = '';
		if ($recursive)
		{
			// Construire un tableau contenant tous les IDs
			$IDs = array();
			$IDs[$objet] = "".$id_objet;
			_gmap_recurse_fils($objet, $id_objet, &$IDs, TRUE);
			
			// Reconstruire la requ�te
			foreach ($IDs as $idObj => $idListe)
			{
				if (strlen($idWhere) > 0)
					$idWhere .= " OR ";
				$idWhere .= "(liens.objet='".$idObj."' AND liens.id_objet IN (".$idListe."))";
			}
		}
		else
			$idWhere = "liens.objet='".$objet."' AND liens.id_objet=".$id_objet;
		if (!strlen($joinLiens))
			$joinLiens = " JOIN spip_gmap_points_liens AS liens ON liens.id_point = points.id_point";
		$clauseWhere .= $idWhere;
	}
	
	// Construction des filtres sur le type de point
	$joinType = "";
	if ($visible)
	{
		if (!strlen($joinType))
			$joinType = " JOIN spip_gmap_types AS types ON types.id_type_point = points.id_type_point";
		if (strlen($clauseWhere))
			$clauseWhere .= " AND ";
		$clauseWhere .= "types.visible='oui'";
	}
	if ($filtre)
	{
		$filtreWhere = "";
		
		// Si c'est une cha�ne la transformer en tableau
		if (strlen($filtre) > 0)
			$filtre = explode(",; ", $filtre);
		
		// Si c'est un tableau
		if (is_array($filtre))
		{
			if ((count($filtre) == 1) && strlen($filtre[0]))
				$filtreWhere = " AND types.nom = '".$filtre[0]."'";
			else if (count($filtre) > 1)
			{
				foreach ($filtre as $filtreItem)
				{
					if (strlen($filtreItem))
					{
						if (strlen($filtreWhere) > 0)
							$filtreWhere .= ",";
						$filtreWhere .= "'".$filtreItem."'";
					}
				}
				if (strlen($filtreWhere) > 0)
					$filtreWhere = " AND types.nom IN (".$filtreWhere.")";
			}
		}
			
		if (!strlen($joinType))
			$joinType = " JOIN spip_gmap_types AS types ON types.id_type_point = points.id_type_point";
		if (strlen($clauseWhere))
			$clauseWhere .= " AND ";
		$clauseWhere .= $filtreWhere;
	}

	// Initialisation du retour
	$count = 0;
	
	// Requ�te sur l'objet
	$rowset = sql_select(
				"count(*) as count",
				"spip_gmap_points AS points".$joinLiens.$joinType,
				$clauseWhere);
	if ($row = sql_fetch($rowset))
		$count += $row['count'];
	sql_free($rowset);
	
	return $count;
}

// Teste si une carte peut �tre affich�e sur un objet
// $objet, $id_objet : identification de l'objet
// $recursive : cherche aussi sous les fils
// Retour : TRUE ou FALSE
function gmap_est_objet_geo($objet, $id_objet, $visible=false, $recursive = false)
{
	// On ne teste pas ici si l'objet est g�olocalisable : m�me s'il a �t� g�olocalis� par le pass�, et ne l'est plus,
	// on consid�re que les positions saisies peuvent �tre affich�es sur une carte. En fait, ce sont aux
	// squelettes de d�terminer si une carte doit �tre affich�e sur un objet ou non, pas au param�trage
	// global.
	return (gmap_compteur($objet, $id_objet, $visible, $recursive) > 0) ? true : false;
}

// Trouver le titre d'un objet (pour afficher dans la bulle ou en survol)
function gmap_marqueur_titre($objet, $id_objet)
{
	$titre = "";
	switch ($objet)
	{
	case "rubrique" :
		$titre = sql_getfetsel("titre", "spip_rubriques", "id_rubrique = ".$id_objet);
		break;
	case "breve" :
		$titre = sql_getfetsel("titre", "spip_breves", "id_breve = ".$id_objet);
		break;
	case "article" :
		$titre = sql_getfetsel("titre", "spip_articles", "id_article = ".$id_objet);
		break;
	case "document" :
		$titre = sql_getfetsel("titre", "spip_documents", "id_document = ".$id_objet);
		break;
	case "auteur" :
		$titre = sql_getfetsel("nom", "spip_auteurs", "id_auteur = ".$id_objet);
		break;
	case "mot" :
		$titre = sql_getfetsel("titre", "spip_mots", "id_mot = ".$id_objet);
		break;
	}
	return $titre;
}

?>