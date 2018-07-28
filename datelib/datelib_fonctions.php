<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
*
*/
function affdate_eval_diff($date, &$diff, &$computed){
	$now = date("U");
	// a day is
	$oneday = 3600 * 24;
	// reconstructing a 'clean' date from what's in the database
	$test_date = preg_match_all(",[0-9]*,",$date,$matches);
	$Y = $matches[0][0];
	$M = $matches[0][2];
	$D = $matches[0][4];
	$computed = date("U", mktime(0,0,0,$M,$D,$Y) );

	// $diff is the number of days between $now and $computed
	$diff =  floor(($now-$computed)/$oneday);

}


/**
 * datelib_fuzzy
 * donne des dates plus sympas par exemple dans les forums
 * @return $date formatée sous forme de date plus ou moins floue
 * @param $date Object
 * marche bien sur http://www.nota-bene.org/ :)
 */
function affdate_fuzzy($date) {
	if($date!='') {

		affdate_eval_diff($date,$diff,$computed);

		// conditionally setting $date
		if($diff < 1) { // then it's today
			$date = _T('datelib:fuzzy_today');
		} else if($diff < 2) { // then it's yesterday
			$date = _T('datelib:fuzzy_yesterday');
		} else if($diff < 7) { // then it's last {weekday}
			$date = _T('datelib:fuzzy_last_w' . date("w",$computed) );
		} else { // too old: resorting to classical affdate display
			$date = affdate($date);
		}
	}
	return $date;
}


/**
 * affdate_progressive
 * formate la date selon les pratques en usage dans la presse
 * @return $date formatée sous forme de date plus ou moins floue
 * @param $date Object
 */
function affdate_progressive($date) {
	if($date!='') {

		affdate_eval_diff($date,$diff,$computed);

		// conditionally setting $date
		if($diff < 1) {
			$date = _T('datelib:fuzzy_today') . " &agrave; ". heures($date)."h".minutes($date);
		} else if($diff < 2) {
			$date = _T('datelib:fuzzy_yesterday') . " &agrave; ". heures($date)." h";
		} else if($diff < 8) {
			$date = _T(nom_jour($date). " &agrave; ". heures($date)." h");
		} else if($diff < 30){
			$date = nom_jour($date)." ".jour($date)." ".nom_mois($date);
		}  else if($diff < 365){
			$date = "le ". jour($date)." ".nom_mois($date);
		}
		else {
			$date = "le ". affdate($date);
		}
	}
	return $date;
}

/**
 * affdate_majs
 * formate la date selon les pratques en usage dans la presse
 * @return $date formatée sous forme de date plus ou moins floue
 * @param $date Object
 */
function affdate_majs($date) {
	if($date!='') {

		affdate_eval_diff($date,$diff,$computed);

		// conditionally setting $date
		if($diff < 1) {
			$date = heures($date)."h".minutes($date) . ", ". _T('datelib:fuzzy_today');
		} else if($diff < 2) {
			$date = heures($date)." h, ". _T('datelib:fuzzy_yesterday');
		} else if($diff < 8) {
			$date = heures($date)." h, ". nom_jour($date);
		} else if($diff < 30){
			$date = nom_jour($date)." ".jour($date)." ".nom_mois($date);
		}  else if($diff < 365){
			$date = jour($date)." ".nom_mois($date);
		}
		else {
			$date = "le ". affdate($date);
		}
	}
	return $date;
}

/**
 * affdate_progressive_court
 * formate la date selon les pratques en usage dans la presse
 * @return $date formatée sous forme de date plus ou moins floue
 * @param $date Object
 */
function affdate_progressive_court($date) {
	if($date!='') {

		affdate_eval_diff($date,$diff,$computed);

		// conditionally setting $date
		if($diff < 1) {
			$date = heures($date)."h".minutes($date);
		} else if($diff < 2) {
			$date = _T('datelib:fuzzy_yesterday') . " &agrave; ". heures($date)." h";
		} else if($diff < 8) {
			$date = _T(nom_jour($date));
		} else if($diff < 30){
			return date("d/m", strtotime($date));
		} else if($diff < 365){
			 	return date("d/m", strtotime($date));
		}	else {
			return date("d/m/y", strtotime($date));
		}
	}
	return $date;
}

?>
