<?php
/**
 * Fichier de filtres communs au plugin Agenda et PIM_agenda
 *
 */

function Agenda_memo_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='')
{
	static $agenda = array();
	if (!$date_deb) {
		$res = $agenda;
		$agenda=array();
		return $res;
	}
	$url=str_replace("&amp;","&",$url);
	
	$idatedeb = date_ical($date_deb);
	$idatefin = date_ical($date_fin);
	$cal = trim($cal); // func_get_args (filtre alterner) rajoute \n !!!!
	$startday1=explode(' ',$date_deb);
	$startday1=$startday1['0'].' 00:00:00';
	$ts_startday1=strtotime($startday1);
	$ts_date_fin=strtotime($date_fin);
	$maxdays=365;
	while (($ts_startday1<$ts_date_fin)&&($maxdays-->0))
	{
		$day=date('Y-m-d H:i:s',$ts_startday1);
		$agenda[$cal][(date_anneemoisjour($day))][] =  array(
			'CATEGORIES' => $cal,
			'DTSTART' => $idatedeb,
			'DTEND' => $idatefin,
			'DESCRIPTION' => $descriptif,
			'SUMMARY' => $titre,
			'LOCATION' => $lieu,
			'URL' => $url);
		$ts_startday1 += 24*3600; // le jour suivant
	}

	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}

function Agenda_memo_evt_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='')
{
	static $evenements = array();
	if (!$date_deb) return $evenements;
	$url=str_replace("&amp;","&",$url);
	
	$idatedeb = date_ical(reset(explode(" ",$date_deb))." 00:00:00");
	$idatefin = date_ical(reset(explode(" ",$date_fin))." 00:00:00");
	$cal = trim($cal); // func_get_args (filtre alterner) rajoute \n !!!!
	$startday1=explode(' ',$date_deb);
	$startday1=$startday1['0'].' 00:00:00';
	$ts_startday1=strtotime($startday1);
	$ts_date_fin=strtotime($date_fin);
	$maxdays=365;
	while (($ts_startday1<$ts_date_fin)&&($maxdays-->0))
	{
		$day=date('Y-m-d H:i:s',$ts_startday1);
		$evenements[$cal][(date_anneemoisjour($day))][] =  array(
			'CATEGORIES' => $cal,
			'DTSTART' => $idatedeb,
			'DTEND' => $idatefin,
			'DESCRIPTION' => $descriptif,
			'SUMMARY' => $titre,
			'LOCATION' => $lieu,
			'URL' => $url);
		$ts_startday1 += 24*3600; // le jour suivant
	}

	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}

function Agenda_affiche_full($i)
{
	$args = func_get_args();
	$nb = array_shift($args); // nombre d'evenements (on pourrait l'afficher)
	$sinon = array_shift($args);
	if (!$nb) return $sinon;
	$type = array_shift($args);
	$agenda = Agenda_memo_full(0);
	$evt_avec = array();
	foreach (($args ? $args : array_keys($agenda)) as $k) {
		if (isset($agenda[$k])&&is_array($agenda[$k]))
			foreach($agenda[$k] as $d => $v) {
				$evt_avec[$d] = isset($evt_avec[$d]) ? (array_merge($evt_avec[$d], $v)) : $v;
			}
	}

	$evenements = Agenda_memo_evt_full(0);
	$evt_sans = array();
	foreach (($args ? $args : array_keys($evenements)) as $k) {
		if (isset($evenements[$k])&&is_array($evenements[$k]))
			foreach($evenements[$k] as $d => $v) {
				$evt_sans[$d] = isset($evt_sans[$d]) ? (array_merge($evt_sans[$d], $v)) : $v;
			}
	}


	if ($type != 'periode')
		$evt = array($evt_sans, $evt_avec);
	else
	{
		$d = array_keys($evt_avec);
		$mindate = min($d);
		$min = substr($mindate,6,2);
		$max = $min + ((strtotime(max($d)) - strtotime($mindate)) / (3600 * 24));
		if ($max < 31) $max = 0;
			$evt = array($evt_sans, $evt_avec, $min, $max);
		$type = 'mois';
	}

	include_spip('inc/agenda');
	$texte=http_calendrier_init('', $type, '', '', self(), $evt);

	return $texte;
}

?>