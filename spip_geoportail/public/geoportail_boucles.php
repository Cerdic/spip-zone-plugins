<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
**/

include_spip('base/geoportail');

// Affichage du popup des zones Geoportail
function geoportail_popup_zone ($name, $selected=null, $class='', $options="", $world=false)
{	if (!$selected || $selected=='') $selected = geoportail_profil('zone');

	$s = "<select class='$class' name='$name' id='$name' $options >";
	eval ('$p = array('._T("geoportail:tzone").');');
	$i=0;
	if ($world) $s .= "<option value='WLD'".('WLD'==$selected ? ' selected="selected">':'>')._T("geoportail:wld")."</option>";
	foreach ($p as $v)
	{	$s .= "<option value='$v'".($v==$selected ? ' selected="selected"':'').">"._T("geoportail:".strtolower($v))."</option>";
	}
	$s.= "</select>\n";
	return $s;
}

/**
 *	Criteres geoposition pour faire la jointure avec la table 
	des positions dans les boucles
 */
function critere_geoposition($idb, &$boucles, $crit)
{
	$boucle = &$boucles[$idb];
	$id = $boucle->primary;
	$table = $boucle->id_table;
	// Identifiant de l'objet de la forme id_nomobjet
	$objet = substr($id, 3);

	// Faire une jointure explicite sur la table des positions
	$t = "geopositions";
	$boucle->from[$t] = 'spip_geopositions';
	$boucle->jointures[] = $t;
	$boucle->jointures_explicites = true;
	
	// Critere de jointure (sur l'objet et sur l'id_objet)
	$boucle->where[]= array ("'='","'$t.objet'","\"'$objet'\"");
	$boucle->where[]= array ("'='","'$t.id_objet'","'$table.$id'");

	return;
}

/** 
	Balise pour la recherche dans le GEORGC a partir des info d'une commune (id_dep et id_com)
*/
function geoportail_get_rgc($id_dep, $id_com='', $what='name')
{	// Ne pas refaire la requete !	
	static $id;
	if ($id[$id_dep.$id_com]) return $id[$id_dep.$id_com][$what];
	// rechercher...
	if ($id_com) $row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc WHERE id_dep='$id_dep' AND id_com='$id_com' AND feature_class!='0'"));
	else $row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc WHERE id_dep='$id_dep'"));
	$id[$id_dep.$id_com]=$row;
	// OK
	return $row[$what];
}

function geoportail_georgc ($p,$what, $dep=false)
{	$id_dep = interprete_argument_balise(1,$p);
	if ($id_dep=='' || $id_dep==NULL) $id_dep = champ_sql('id_dep', $p);
	if ($dep) $id_com = "''";
	else
	{	$id_com = interprete_argument_balise(2,$p);
		if ($id_com=='' || $id_com==NULL) $id_com = champ_sql('id_com', $p);
	}
	$p->code = 'geoportail_get_rgc('.$id_dep.','.$id_com.',\''.$what.'\')';
	// Recherche dans la boucle du dessus
	$p->interdire_scripts = false;
	return $p;
}

function balise_NOM_DEP_dist ($p) 
{	return geoportail_georgc($p,'name', true);
}

function balise_NOM_COM_dist ($p) 
{	return geoportail_georgc($p,'name');
}

function balise_TOP25_dist ($p) 
{	return geoportail_georgc($p,'map');
}

function balise_POPULATION_dist ($p) 
{	return geoportail_georgc($p,'population');
}

function balise_SURFACE_dist ($p) 
{	return geoportail_georgc($p,'surface');
}

/** Critere departement pour les boucles georgc
	permet de se limiter a la recherche d'un departement.
*/
function critere_departement($idb, &$boucles, $crit)
{	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	// Critere 
	$boucle->where[]= array ("'='","'$table.feature_class'","0");
	return;
}

/** Recherche du nom de departement 
*/
function geoportail_departement($d)
{	$query = "SELECT * FROM spip_georgc WHERE feature_class = '0' AND id_dep = '".$d."'";
	$res = spip_query($query);
	$row = spip_fetch_array($res);

	$rep = $row['name'];
	return $rep;
}

/*========================================
  Filtres du plugin
========================================*/
/**
*/
function geoportail_defaut ($mode)
{	$geoportail_provider = $GLOBALS['meta']['geoportail_provider'];
	if ($geoportail_provider && !$mode) return $geoportail_provider;
	return $mode;
}

/** Recherche d'un document ou renvoie l'url
*/
function geoservice_url ($url)
{	if (preg_match('/^doc(\d+)/',$url,$match)) 
	{	$id = intval($match[1]);
	/*
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document=$id LIMIT 0,1"));
		return _DIR_IMG.$row['fichier'];
	*/
		include_spip('urls/standard');
		return (generer_url_document($id));
	}
	else return $url;
}

/** Transformation degre/minute/seconde
*/
function geoportail_dms($l, $short=false)
{	if (!is_numeric($l)) return $l;
	
	$d = floor($l);
	$p = ($l-$d)*60;
	$m = floor($p);
	if ($m<10) $m = "0$m";
	$s = round((($p-$m)*600))/10;
	if ($s<10) $s = "0$s";
	$str = "$d&deg; $m' $s\"" ;
	if ($short) $str = str_replace (' ','',$str);
	return $str;
}

/** Transformation #LON|geoportail_longitude
*/
function geoportail_longitude ($l, $short=false)
{	if (is_numeric($l))
	{	if ($l<0) return geoportail_dms(-$l,$short).($short?'':' ')."W";
		else return geoportail_dms($l,$short).($short?'':' ')."E";
	}
	return $l;
}

/** Transformation #LAT|geoportail_latitude
*/
function geoportail_latitude ($l, $short=false)
{	if (is_numeric($l))
	{	if ($l<0) return geoportail_dms(-$l,$short).($short?'':' ')."S";
		else return geoportail_dms($l,$short).($short?'':' ')."N";
	}
	return $l;
}

/** 
	Gestion des regions : renvoie les departements d'une region 
	(pour utilisation dans un critere de boucle : {id_dep IN #REGION|geoportail_region}
*/
function geoportail_region ($region)
{	// Nom de la region en minuscule sans accent et sans -
	$region = str_replace('-',' ',strtolower(translitteration($region)));

	// Renvoyer les departements composant la region
	switch ($region)
	{	case 'alsace':
		case '42':
		case 'strasbourg':
			return array('67','68');
			
		case 'aquitaine':
		case '72':
		case 'bordeaux':
			return array('24','33','40','47','64');
			
		case 'auvergne':
		case '83':
		case 'clermont ferrand':
			return array('03','15','43','63');
			
		case 'basse normandie':
		case '25':
		case 'caen':
			return array('14','50','61');
			
		case 'bourgogne':
		case '26':
		case 'dijon':
			return array('21','58','71','89');
			
		case 'bretagne':
		case '53':
		case 'rennes':
			return array('22','29','35','56');

		case 'centre':
		case '24':
		case 'orleans':
			return array('18','28','36','37','41','45');
			
		case 'champagne ardenne':
		case 'champagne':
		case 'ardenne':
		case '21':
		case 'chalons en champagne':
			return array('24','33','40','47','64');
			
		case 'corse':
		case '94':
		case 'ajaccio':
			return array('2A','2B');
		
		case 'franche comte':
		case 'comte':
		case '43':
		case 'besancon':
			return array('25','39','70','90');
		
		case 'guadeloupe':
		case '01':
		case 'basse terre':
			return array('971');
		
		case 'guyane':
		case '03':
		case 'cayenne':
			return array('973');
		
		case 'haute normandie':
		case '23':
		case 'rouen':
			return array('14','50','61');
		
		case 'ile de france':
		case '11':
		case 'paris':
			return array('75','77','78','91','92','93','94');
		
		case 'la reunion':
		case 'reunion':
		case '04':
		case 'saint denis':
			return array('974');
		
		case 'languedoc roussillon':
		case 'languedoc':
		case 'roussillon':
		case '91':
		case 'montpellier':
			return array('11','30','34','48','66');
			
		case 'limousin':
		case '74':
		case 'limoges':
			return array('19','23','87');
		
		case 'lorraine':
		case '41':
		case 'metz':
			return array('54','55','57','88');
		
		case 'martinique':
		case '02':
		case 'fort de france':
			return array('972');
		
		case 'mayotte':
		case '06':
		case 'dzaoudzi':
			return array('976');
		
		case 'midi pyrenees':
		case 'pyrenees':
		case '73':
		case 'toulouse':
			return array('25','39','70','90');
		
		case 'nord pas de calais':
		case 'nord':
		case 'calais':
		case '31':
		case 'lille':
			return array('59','62');
			
		case 'pays de la loire':
		case 'pays de loire':
		case 'loire':
		case '52':
		case 'nantes':
			return array('44','49','53','72','85');
		
		case 'picardie':
		case '22':
		case 'amiens':
			return array('02','60','80');
		
		case 'poitou charentes':
		case 'poitou':
		case 'charentes':
		case '54':
		case 'poitiers':
			return array('16','17','79','86');
		
		case 'provence alpes cote d\'azur':
		case 'provence':
		case 'alpes cote d\'azur':
		case 'cote d\'azur':
		case '93':
		case 'marseille':
			return array('25','39','70','90');
		
		case 'rhone alpes':
		case 'rhone':
		case '82':
		case 'lyon':
			return array('01','07','26','38','42','69','73','74');
		
		default: return array('0');
/* */
	}
}
/* */
?>