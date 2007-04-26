<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */
include_spip('inc/geoforms');

function balise_LATITUDE_dist($p){
	$_id_donnee = champ_sql('id_donnee', $p);
	$_id_form = champ_sql('id_form', $p);
	$p->code = "(geoforms_donnee_latitude($_id_form,$_id_donnee))";
	$p->interdire_scripts = false; // securite assuree par la fonction

	return $p;
}
function balise_LONGITUDE_dist($p){
	$_id_donnee = champ_sql('id_donnee', $p);
	$_id_form = champ_sql('id_form', $p);
	$p->code = "(geoforms_donnee_longitude($_id_form,$_id_donnee))";
	$p->interdire_scripts = false; // securite assuree par la fonction

	return $p;
}
function balise_GEOICONE_dist($p){
	$_id_donnee = champ_sql('id_donnee', $p);
	$_id_form = champ_sql('id_form', $p);
	$p->code = "(geoforms_donnee_icone($_id_form,$_id_donnee))";
	$p->interdire_scripts = false; // securite assuree par la fonction

	return $p;
}

function geoforms_donnee_icone($id_form,$id_donnee){
	return url_absolue(find_in_path('img_pack/correxir.png'));
}

function geoforms_donnee_latitude($id_form,$id_donnee){
	$t = geoforms_donnee_latitude_longitude($id_form,$id_donnee);
	return reset($t);
}
function geoforms_donnee_longitude($id_form,$id_donnee){
	$t = geoforms_donnee_latitude_longitude($id_form,$id_donnee);
	return end($t);
}
function geoforms_donnee_latitude_longitude($id_form,$id_donnee){
	static $buf = array();
	if (!isset($buf[$id_donnee])){
		$res = spip_query("SELECT c.*,dc.*
		FROM spip_forms_champs as c, spip_forms_donnees_champs as dc 
		WHERE c.type IN ('geox','geoy') AND c.id_form="._q($id_form)." AND dc.champ=c.champ AND dc.id_donnee="._q($id_donnee)." ORDER BY c.rang");
		while ($row = spip_fetch_array($res) AND (!isset($buf[$id_donnee]['geox']) OR !isset($buf[$id_donnee]['geoy']))){
			$buf[$id_donnee][$row['type']] = array('value'=>$row['valeur'],'syst'=>$row['extra_info']);
		}
		list($buf[$id_donnee]['geox'],$buf[$id_donnee]['geoy']) = geoforms_latitude_longitude($buf[$id_donnee]['geox']['value'],$buf[$id_donnee]['geoy']['value'],$buf[$id_donnee]['geox']['syst']);
	}
	if (!isset($buf[$id_donnee])) return array(0,0);
	return array($buf[$id_donnee]['geox'],$buf[$id_donnee]['geoy']);
}

?>