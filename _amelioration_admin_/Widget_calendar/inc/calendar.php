<?php

define('_DIR_PLUGIN_WIDGET_CALENDAR',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)."/.."))))));

global $WCalendar_independants,$WCalendar_lies;
$WCalendar_independants=array();
$WCalendar_lies=array();

function WCalendar_ajoute($titre,$suffixe){
	global $WCalendar_independants;	
	$WCalendar_independants[] = array('titre'=>$titre,'suffixe'=>$suffixe);
}
function WCalendar_ajoute_lies($titre_debut,$suffixe_debut,$titre_fin,$suffixe_fin){
	global $WCalendar_lies;	
	$WCalendar_lies[] = array('titre1'=>$titre_debut,'suffixe1'=>$suffixe_debut,
														'titre2'=>$titre_fin,'suffixe2'=>$suffixe_fin);
}

function WCalendar_controller($date,$suffixe){
	if (strcmp($date,format_mysql_date())==0)
		$date=date("Y-m-d H:i:s");
	$s = "<a href='javascript:void(null)' onclick='showCalendar$suffixe()'>
	<img id='dateLink$suffixe' src='"._DIR_IMG_PACK."/cal-jour.gif' border='0' style='vertical-align:middle;margin:5px'/></a>
	";
	$s .=
	  afficher_jour(jour($date), "id='jour$suffixe' name='jour$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_mois(mois($date), "id='mois$suffixe' name='mois$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_annee(annee($date), "id='annee$suffixe' name='annee$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'", date('Y')-4);
  return $s;
}

?>