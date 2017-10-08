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

function Agenda_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui'){
	static $trans_tbl=NULL;
	if ($trans_tbl==NULL){
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$s = "";
	if (($d=date("Y-m-d",$date_debut))==date("Y-m-d",$date_fin))
	{ // meme jour
		$s = ucfirst(nom_jour($d))." ".affdate_jourcourt($d);
		if ($horaire=='oui'){
			$s .= " ".($hd=date("H:i",$date_debut));
			if ($hd!=($hf=date("H:i",$date_fin)))
				$s .= "-$hf";
		}
	}
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		$d=date("Y-m-d",$date_debut);
		if ($horaire=='oui'){
			$s = _T('agenda:evenement_date_du') . " " . affdate_jourcourt($d);
			$s .= " ".($hd=date("H:i",$date_debut));
			$s .= " " . _T('agenda:evenement_date_au').date("d  H:i ",$date_fin);
		}
		else {
			$s = _T('agenda:evenement_date_du') . " " . jour($d);
			$s .= " " . _T('agenda:evenement_date_au').affdate_jourcourt(date("Y-m-d",$date_fin));
		}
	}
	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		$d=date("Y-m-d",$date_debut);
		$s = _T('agenda:evenement_date_du') . " " . affdate_jourcourt($d);
		if ($horaire=='oui')
			$s .= " ".date("H:i",$date_debut);
		$d = date("Y-m-d",$date_fin);
		$s .= " "._T('agenda:evenement_date_au').affdate_jourcourt($d);
		if ($horaire=='oui')
			$s .= " ".date("H:i",$date_fin);
	}
	else
	{ // tout different
		$s = _T('agenda:evenement_date_du') . " " . affdate($d);
		if ($horaire=='oui')
			$s .= " ".date("(H:i)",$date_debut);
		$d = date("Y-m-d",$date_fin);
		$s .= " "._T('agenda:evenement_date_au').affdate($d);
		if ($horaire=='oui')
			$s .= " ".date("(H:i)",$date_fin);
	}
	return unicode2charset(charset2unicode(strtr($s,$trans_tbl),''));	
}

function Agenda_dateplus($date,$secondes,$format){
	$date = strtotime($date)+eval("return $secondes;"); // permet de passer une expression
	return date($format,$date);
}

// decale les mois de la date.
// cette fonction peut raboter le jour si le nouveau mois ne les contient pas
// exemple 31/01/2007 + 1 mois => 28/02/2007
function Agenda_moisdecal($date,$decalage,$format){
	include_spip('inc/filtres');
	$date_array = recup_date($date);
	if ($date_array) list($annee, $mois, $jour) = $date_array;
	if (!$jour) $jour=1;
	if (!$mois) $mois=1;
	$mois2 = $mois + $decalage;
	$date2 = mktime(1, 1, 1, $mois2, $jour, $annee);
	// mois normalement attendu
	$mois3 = date('m', mktime(1, 1, 1, $mois2, 1, $annee));
	// et si le mois de la nouvelle date a moins de jours...
	$mois2 = date('m', $date2);
	if ($mois2 - $mois3) $date2 = mktime(1, 1, 1, $mois2, 0, $annee);
	return date($format, $date2);
}

?>