<?php

/*

	Les fonctions génériques de calcul de date du calendrier

*/

//renvoie un jour de semaine compatible avec SPIP
function jour_semaine($jour) {
	return ((($jour-1)%7)+1);
}

/**
 * fonction 'coupe'
 * coupe un 'mot' aux 'nbcar' premiers caracteres, en comptant
 * les eventuelles chaines (mots html) de la forme 'car_deb'*'car_fin'
 * comme de simples caracteres (&*; par defaut).
 */
function coupe ($mot, $nbcar, $car_deb='&', $car_fin=';') {
	//decoupage brut
	$sousmot = substr($mot,0,$nbcar);
	// si on n'a pas de debut de mot html
	if (!($pos_cardeb = strpos($sousmot, $car_deb)))
		// pas de traitement specifique
		return $sousmot;
	// sinon, on traite le premier mot html contenu
	// fin du premier mot trouve :
	$pos_carfin = $pos_cardeb + strpos(substr($mot,$pos_cardeb),$car_fin);
	// et comme cette fonction permet de traiter elle-meme la suite..
	return substr($mot,0,$pos_carfin+1) . coupe(substr($mot,$pos_carfin+1),$nbcar-($pos_cardeb+1));
}

//fournit le jour de semaine selon son numero
// - en entier/abrege/initiale/1-3caracteres
// - dans la langue ad'hoc
function date_jour($jour, $abbr='') {
	$res = 'date_jour_'.$jour;
	switch ($abbr) {
	case "1car" :
	case "2car" :
	case "3car" :
		$nb_car = $abbr{0};
		$res = coupe(_T($res), $nb_car);
		break;
	case "abbr" :
	case "initiale" :
		$res .= '_'.$abbr;
	default :
		$res = _T($res);
	}
	return $res;
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
function est_passee($date){
	return strtotime($date) - time() < 1 ? ' ' : '';
}

//compat SPIP 1.9.2
if(!function_exists('push')) {
	function push($array, $val) {
		if($array == '' OR !array_push($array, $val)) return '';
		return $array;
	}
}

if(!function_exists('find')) {
	function find($array, $val) {
		return ($array != '' AND in_array($val, $array));
	}
}

?>
