<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/filtres');
/*
** À partir d'un tableau de propriété de date ical, retourne deux infos:
** 1. Date formatée en sql
** 2. booleen pour savoir si toute la journée
** Possibilité de ne retourner qu'une info si on seulement_date à True
*/
function date_ical_to_sql($date,$decalage=array(),$seulement_date=false){
	if (isset($date['value'])){
		$value = $date['value'];
	}
	else{
		$value = $date;
	}
	if (isset($date['params']) and $params = $date['params'] and is_array($params) and in_array('DATE',$params)){
		$all_day = True;
		$date_sql = sql_format_date(
			$value['year'],
			$value['month'],
			$value['day']
		);
	}
	else{
		$all_day = False;
		$date_sql = sql_format_date(
			$value['year'],
			$value['month'],
			$value['day'],
			$value['hour'],
			$value['min'],
			$value['sec']
		);
	}
	$date_ete = intval(affdate($date_sql,'I'));//Est-on en heure d'été?
	if (!$all_day and is_array($decalage)
		and isset($decalage['ete'])
		and isset($decalage['hiver'])){
			if ($date_ete){
				$decalage = $decalage['ete'];
			}
			else{
				$decalage = $decalage['hiver'];
			}
			$date_sql = "DATE_ADD('$date_sql', INTERVAL $decalage HOUR)";
	}
	if ($seulement_date){
		return $date_sql;
	}
	else{
		return array($date_sql,$all_day);
	}
}
