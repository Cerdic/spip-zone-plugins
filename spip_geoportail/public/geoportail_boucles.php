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
function geoportail_popup_zone ($name, $selected=null, $class='', $options="")
{	if (!$selected || $selected=='') $selected = geoportail_profil('zone');

	$s = "<select class='$class' name='$name' id='$name' $options >";
	eval ('$p = array('._T("geoportail:tzone").');');
	$i=0;
	foreach ($p as $v)
	{	$s .= "<option value='$v'".($v==$selected ? ' SELECTED':'').">"._T("geoportail:".strtolower($v))."</option>";
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
?>