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

?>