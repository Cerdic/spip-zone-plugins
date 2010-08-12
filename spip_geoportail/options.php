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
* Options utiles + autorisation pour le positionnement des auteurs
*
**/

// Affichage logo du service
if ($GLOBALS['spip_version_code'] > 1.99) 
{	charger_fonction('iconifier', 'inc');
	$GLOBALS['logo_libelles']['id_geoservice'] = _T('geoportail:logo_service');
}

/**
* API d'autorisation
* Un auteur peut modifier son geopositionnement
*/
function autoriser_auteur_positionner ($faire, $type, $id, $qui, $opt)
{	return 
	(	(($qui['statut'] == '0minirezo') && !$qui['restreint'])
	||	($qui['id_auteur'] == $id)
	);
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

function geoportail_longitude ($l, $short=false)
{	if (is_numeric($l))
	{	if ($l<0) return geoportail_dms(-$l,$short).($short?'':' ')."W";
		else return geoportail_dms($l,$short).($short?'':' ')."E";
	}
	return $l;
}

function geoportail_latitude ($l, $short=false)
{	if (is_numeric($l))
	{	if ($l<0) return geoportail_dms(-$l,$short).($short?'':' ')."S";
		else return geoportail_dms($l,$short).($short?'':' ')."N";
	}
	return $l;
}

?>