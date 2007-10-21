<?php

// surcharge de agenda_affiche pour le #CALENDRIER 
// http://doc.spip.org/@agenda_calendrier
function agenda_calendrier($date) {
	$jour = affdate($date, 'jour');
	set_request('jour', $jour?$jour:1);
	set_request('mois', affdate($date, 'mois'));
	set_request('annee', affdate($date, 'annee'));
	return agenda_affiche(1, '', 'mois_unique');
}

//
// fonction standard de calcul de la balise #CALENDRIER
// on peut la surcharger en definissant dans mes_fonctions :
// function calendrier($plage, $nom, $bloc_cal, $modele) {...}
//

// http://doc.spip.org/@calcul_calendrier
function calcul_calendrier($plage, $nom, $bloc_cal = true, $modele = 'articles_mois'){
	static $ancres = array();
	$bloc_ancre = "";

	if (function_exists("calendrier"))
		return calendrier($plage, $nom, $bloc_cal, $modele);

	$date = 'date'.$nom;
	$ancre = 'calendrier'.$nom;

	// n'afficher l'ancre qu'une fois
	if (!isset($ancres[$ancre]))
		$bloc_ancre = $ancres[$ancre] = "<a name='$ancre' id='$ancre'></a>";

	$calendrier = array(
		'var_date' => $date,
		'date' => $plage ? $plage : date('Y-m'),
		'ancre' => $ancre,
		'bloc_ancre' => $bloc_ancre,
		'self' => parametre_url(self(),'fragment','')
	);

	// liste = false : on ne veut que l'ancre
	if (!$bloc_cal)
		return $bloc_ancre;

	return recuperer_fond("modeles/calendrier_$modele",$calendrier);
}

/*

	Les fonctions génériques de calcul de date du calendrier

*/

//renvoie un jour de semaine compatible avec SPIP
function jour_semaine($jour) {
	return ((($jour-1)%7)+1);
}

function date_jour($jour, $abbr='') {
	return _T('date_jour_'.$jour.($abbr ? '_'.$abbr : ''));
}

function minical_compteur($code) {
	static $cpt = 0;
	return $code.(++$cpt);
}

//format acceptes: AAAA, AAAA-MM, AAAA-MM-JJ
function date_amj($date) {
	$annee = substr($date, 0, 4);
	$mois = ($m = substr($date, 5, 2)) ? $m : '01';
	$jour = ($j = substr($date, 8, 2)) ? $j : '01';
	return array($annee, $mois, $jour);
}

function amj_date($amj, $format = 'Y-m-d') {
	list($annee, $mois, $jour) = $amj;
	return date($format, mktime(12, 0, 0, $mois, $jour, $annee));
}

function date_voisine($date, $decal=0, $unite='', $format='Y-m-d') {
	list($annee, $mois, $jour) = date_amj($date);
	if($unite == 'jour' OR $unite == 'jours') $jour = $jour + $decal;
	if($unite == 'mois') $mois = $mois + $decal;
	if($unite == 'annee' OR $unite == 'annees') $annee = $annee + $decal;
	$amj = array($annee, $mois, $jour);
	return amj_date($amj, $format);
}

function jour_precedent($date) {
	return date_voisine($date, -1, 'jour');
}

function jour_suivant($date) {
	return date_voisine($date, +1, 'jour');
}

function mois_precedent($date) {
	return date_voisine($date, -1, 'mois', 'Y-m');
}

function mois_suivant($date) {
	return date_voisine($date, +1, 'mois', 'Y-m');
}

function annee_precedente($date) {
	return date_voisine($date, -1, 'annee', 'Y-m');
}

function annee_suivante($date) {
	return date_voisine($date, +1, 'annee', 'Y-m');
}

function debut_semaine($date, $pjc = 2) {
	list($annee, $mois, $jour) = date_amj($date);
	$w_day = date("w", mktime(12,0,0,$mois, $jour, $annee));
	if ($w_day == 0) $w_day = 7; // Gaffe: le dimanche est zero
	$debut = $jour-$w_day+$pjc-1;
	$amj = array($annee, $mois, $debut);
	return amj_date($amj);
}

function debut_mois($date) {
	if($date == null) return '';
	list($annee, $mois, $jour) = date_amj($date);
	$amj = array($annee, $mois, 1);
	return amj_date($amj);
}

function numero_semaine($date) {
	list($annee, $mois, $jour) = date_amj($date);
	$semaine = date("W", mktime(12,0,0,$mois, $jour, $annee));
	return sprintf('%02d', $semaine);
}

function teste_mois($date, $date_test) {
	$test = strpos($date_test, '-') ? affdate($date_test, 'mois') : $date_test;
	return (affdate($date, 'mois') == $test) ? $date : '';
}

function est_aujourdhui($date) {
	return affdate($date, 'Y-m-d') == date('Y-m-d') ? $date : '';
}

?>
