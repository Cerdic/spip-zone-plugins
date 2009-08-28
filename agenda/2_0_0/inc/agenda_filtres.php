<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 * Fichier de filtres communs au plugin agenda et PIM_agenda
 */

/**
 * Afficher une de facon textuelle les dates de debut et fin en fonction des cas
 * - Le lundi 20 fevrier � 18h
 * - Le 20 f�vrier de 18h � 20h
 * - Du 20 au 23 f�vrier
 * - du 20 fevrier au 30 mars
 * - du 20 fevrier 2007 au 30 mars 2008
 * $horaire='oui' permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * $forme peut contenir abbr (afficher le nom des jours en abbrege) et ou hcal (generer une date au format hcal)
 * 
 * @param string $date_debut
 * @param string $date_fin
 * @param string $horaire
 * @param string $forme
 * @return string
 */
function agenda_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme=''){
	static $trans_tbl=NULL;
	if ($trans_tbl==NULL){
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
	}
	
	$abbr = '';
	if (strpos($forme,'abbr')!==false) $abbr = 'abbr';
	
	$dtstart = $dtend = $dtabbr = "";
	if (strpos($forme,'hcal')!==false) {
		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
		$dtabbr = "</abbr>";
	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = $horaire=='oui';
	$hd = date("H:i",$date_debut);
	$hf = date("H:i",$date_fin);
	$au = " " . strtolower(_T('agenda:evenement_date_au'));
	$du = _T('agenda:evenement_date_du') . " ";
	$s = "";
	if ($d==$f)
	{ // meme jour
		$s = ucfirst(nom_jour($d,$abbr))." ".affdate_jourcourt($d);
		if ($h)
			$s .= " $hd";
		$s = "$dtstart$s$dtabbr";
		if ($h AND $hd!=$hf) $s .= "-$dtend$hf$dtabbr";
	}
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		if ($h){
			$s = $du . $dtstart . affdate_jourcourt($d) . " $hd" . $dtabbr;
			$s .= $au . $dtend . affdate_jourcourt($f);
			if ($hd!=$hf) $s .= " $hf";
			$s .= $dtabbr;
		}
		else {
			$s = $du . $dtstart . jour($d) . $dtabbr;
			$s .= $au . $dtend . affdate_jourcourt($f) . $dtabbr;
		}
	}
	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		$s = $du . $dtstart . affdate_jourcourt($d);
		if ($h) $s .= " $hd";
		$s .= $dtabbr . $au . $dtend . affdate_jourcourt($f);
		if ($h) $s .= " $hf";
		$s .= $dtabbr;
	}
	else
	{ // tout different
		$s = $du . $dtstart . affdate($d);
		if ($h)
			$s .= " ".date("(H:i)",$date_debut);
		$s .= $dtabbr . $au . $dtend. affdate($f);
		if ($h)
			$s .= " ".date("(H:i)",$date_fin);
		$s .= $dtabbr;
	}
	return unicode2charset(charset2unicode(strtr($s,$trans_tbl),''));	
}

function agenda_dateplus($date,$secondes,$format){
	$date = strtotime($date)+eval("return $secondes;"); // permet de passer une expression
	return date($format,$date);
}

/**
 * Afficher un message "une truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function agenda_affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	if (!$nb=intval($nb)) return "";
	if ($nb>1) return _T($chaine_plusieurs, array($var => $nb));
	else return _T($chaine_un);
}

function agenda_memo_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='')
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
	while (($ts_startday1<=$ts_date_fin)&&($maxdays-->0))
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
		$ts_startday1 += 26*3600; // le jour suivant : +26 h pour gerer les changements d'heure
		$ts_startday1 = mktime(0, 0, 0, date("m",$ts_startday1), 
		date("d",$ts_startday1), date("Y",$ts_startday1)); // et remise a zero de l'heure	
	}
	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}

function agenda_memo_evt_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='')
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
	while (($ts_startday1<=$ts_date_fin)&&($maxdays-->0))
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
		$ts_startday1 += 26*3600; // le jour suivant : +26 h pour gerer les changements d'heure
		$ts_startday1 = mktime(0, 0, 0, date("m",$ts_startday1), 
		date("d",$ts_startday1), date("Y",$ts_startday1)); // et remise a zero de l'heure	
	}

	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}

function agenda_affiche_full($i)
{
	/* On recupere la date a afficher dans le contexte SPIP */
	global $contexte;
	$mois = $contexte['mois'];
	$jour = $contexte['jour'];
	$annee = $contexte['annee'];
	if (!$mois || !$jour || !$annee) {
		$date_agenda = ''; /* si il manque un des elements, date vide, http_calendrier_init mettra la date courante */
	}
	else { /* sinon on cree une date a partir des informations du contexte pour la donner en parametre a http_calendrier_init */
		$date_agenda = mktime(0,0,0,$mois,$jour,$annee);
	}

	$args = func_get_args();
	$nb = array_shift($args); // nombre d'evenements (on pourrait l'afficher)
	$sinon = array_shift($args);
	if (!$nb) return $sinon;
	$type = array_shift($args);
	$agenda = agenda_memo_full(0);
	$evt_avec = array();
	foreach (($args ? $args : array_keys($agenda)) as $k) {
		if (isset($agenda[$k])&&is_array($agenda[$k]))
			foreach($agenda[$k] as $d => $v) {
				$evt_avec[$d] = isset($evt_avec[$d]) ? (array_merge($evt_avec[$d], $v)) : $v;
			}
	}

	$evenements = agenda_memo_evt_full(0);
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
	$texte=http_calendrier_init($date_agenda, $type, '', '', self(), $evt);

	return $texte;
}


// decale les mois de la date.
// cette fonction peut raboter le jour si le nouveau mois ne les contient pas
// exemple 31/01/2007 + 1 mois => 28/02/2007
function agenda_moisdecal($date,$decalage,$format){
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


// decale les jours de la date.
function agenda_jourdecal($date,$decalage,$format){
	include_spip('inc/filtres');
	$date_array = recup_date($date);
	if ($date_array) list($annee, $mois, $jour) = $date_array;
	if (!$jour) $jour=1;
	if (!$mois) $mois=1;
	$jour2 = $jour + $decalage;
	$date2 = mktime(1, 1, 1, $mois, $jour2, $annee);
	return date($format, $date2);
}

?>
