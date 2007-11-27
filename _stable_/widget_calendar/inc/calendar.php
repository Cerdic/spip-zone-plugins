<?php

if (!defined('_DIR_PLUGIN_WCALENDAR')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_WCALENDAR',(_DIR_PLUGINS.end($p)));
}

define('_WIDGET_CALENDAR_BACK_IN_TIME',4);

global $WCalendar_independants,$WCalendar_lies,$WCalendar_statiques;
$WCalendar_independants=array();
$WCalendar_lies=array();
$WCalendar_statiques=array();

function WCalendar_ajoute($titre,$suffixe){
	global $WCalendar_independants;	
	$WCalendar_independants[] = array('titre'=>$titre,'suffixe'=>$suffixe);
}
function WCalendar_ajoute_lies($titre_debut,$suffixe_debut,$titre_fin,$suffixe_fin){
	global $WCalendar_lies;	
	$WCalendar_lies[] = array('titre1'=>$titre_debut,'suffixe1'=>$suffixe_debut,
														'titre2'=>$titre_fin,'suffixe2'=>$suffixe_fin);
}
function WCalendar_ajoute_statique($titre,$suffixe){
	global $WCalendar_statiques;	
	$WCalendar_statiques[] = array('titre'=>$titre,'suffixe'=>$suffixe);
}
function WCalendar_statique_point_entree($suffixe, $dates = ""){
	return "<div><div id='container$suffixe' style='z-index:5000;'></div>
	<div style='display:none;'><textarea id='selected_date$suffixe' name='selected_date$suffixe' rows='3' cols='40'>$dates</textarea></div>
	<a href='javascript:cal$suffixe.reset()'>Reset</a>"
	//. "<a href='javascript:alert(cal$suffixe.getSelectedDates())'>what's selected?</a>"
	. "</div>
	";
}


function WCalendar_header($flux,$onload=""){
	global $init_functions;
	$init_functions = $onload;
	include_spip('inc/calendar_init');
	return WCalendar_header_prive($flux);
}
function WCalendar_body($flux){
	include_spip('inc/calendar_init');
	return WCalendar_body_prive($flux);
}

function WCalendar_controller($date,$suffixe){
	if (strcmp($date,format_mysql_date())==0)
		$date=date("Y-m-d H:i:s");
	$s = "<a href='javascript:void(null)' onclick='showCalendar$suffixe()'>
	<img id='dateLink$suffixe' src='"._DIR_IMG_PACK."cal-jour.gif' style='border:none;vertical-align:middle;margin:5px' alt=''/></a>
	";
	$s .=
	  afficher_jour(jour($date), "id='jour$suffixe' name='jour$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_mois(mois($date), "id='mois$suffixe' name='mois$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_annee(annee($date), "id='annee$suffixe' name='annee$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'", date('Y')-_WIDGET_CALENDAR_BACK_IN_TIME);
  return $s;
}

function WCalendar_statique_controller($dates,$suffixe){
	if (is_array($dates))
		$dates = implode(',',$dates);
  return WCalendar_statique_point_entree($suffixe, $dates);
}

?>
