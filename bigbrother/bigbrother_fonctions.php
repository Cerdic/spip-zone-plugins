<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre : duree
 *   +----------------------------------+
 *    date : 2008.01.10
 *    auteur :  erational - http://www.erational.org
 *    version: 0.25
 *    licence: GPL
 *   +-------------------------------------+
 *
 *    retourne la duree entre 2 dates
 *
 *    parametres type_affichage
 *    - court   : 5 jours (par defaut)
 *    - etendu  : 4 semaines 3 jours 23 heures 2 minutes
 *    - horaire : 4h39
 *    - minute  : 124 (minutes cumulees)
 *    - iso8601 : P18Y9W4DT11H9M8S   ref. http://fr.wikipedia.org/wiki/ISO_8601#Dur.C3.A9e
 *    - ical    : P18Y9W4DT11H9M8S   ref. http://tools.ietf.org/html/rfc2445#page-37 (mm chose que iso)
 *
 *    pour sortir une valeur uniquement (i18n)
 *    - Y       : (an)
 *    - W       : (semaine)
 *    - D       : (jour)
 *    - H       : (heure)
 *    - M       : (minute)
 *    - S       : (s)
 *
*/

// On le préfixe tout de même, pour être sûr de pas faire de conflits
function bigbrother_duree($date_debut,$date_fin,$type_affichage='court') {

	// Si ce n'est QUE une suite de chiffres, c'est un timestamp direct
	$date_debut = preg_match('/^[0123456789]+$/', $date_debut) ? intval($date_debut) : strtotime($date_debut);
	$date_fin = preg_match('/^[0123456789]+$/', $date_fin) ? intval($date_fin) : strtotime($date_fin);

	// S'il n'y a pas de date de debut, on ne fait rien
	if (!$date_debut)
		return "";
	// S'il n'y a pas de date de fin, on considère que $date_debut est la durée à afficher
	elseif (!$date_fin)
		$diff_seconds = $date_debut;
	// Sinon on fait bien la différence
	else
		$diff_seconds = $date_fin - $date_debut;

	if ($diff_seconds<0)
		return "";

	// Si on demande la durée en secondes, on quitte tout de suite
	if ($type_affichage == "secondes")
		return $diff_seconds;

	$diff_years    = floor($diff_seconds/31536000);
	$diff_seconds -= $diff_years   * 31536000;

	$diff_weeks    = floor($diff_seconds/604800);
	$diff_seconds -= $diff_weeks   * 604800;

	$diff_days     = floor($diff_seconds/86400);
	$diff_seconds -= $diff_days    * 86400;

	$diff_hours    = floor($diff_seconds/3600);
	$diff_seconds -= $diff_hours   * 3600;

	$diff_minutes  = floor($diff_seconds/60);
	$diff_seconds -= $diff_minutes * 60;

	$str = "";

	switch ($type_affichage) {

		case "court" :
			if ($diff_years>1) $str = "$diff_years ans";
			else if ($diff_years>0) $str = "$diff_years an";
			else if ($diff_weeks>1) $str = "$diff_weeks semaines";
			else if ($diff_weeks>0) $str = "$diff_weeks semaine";
			else if ($diff_days>1) $str = "$diff_days jours";
			else if ($diff_days>0) $str = "$diff_days jour";
			else if ($diff_hours>1) $str = "$diff_hours heures";
			else if ($diff_hours>0) $str = "$diff_hours heure";
			else if ($diff_minutes>1) $str = "$diff_minutes minutes";
			else if ($diff_minutes>0) $str = "$diff_hours minute";
			break;

		case "etendu" :
			if ($diff_years>1) $str .= "$diff_years ans ";
			else if ($diff_years>0) $str .= "$diff_years an ";
			if ($diff_weeks>1) $str .= "$diff_weeks semaines ";
			else if ($diff_weeks>0) $str .= "$diff_weeks semaine ";
			if ($diff_days>1) $str .= "$diff_days jours ";
			else if ($diff_days>0) $str .= "$diff_days jour ";
			if ($diff_hours>1) $str .= "$diff_hours heures ";
			else if ($diff_hours>0) $str .= "$diff_hours heure ";
			if ($diff_minutes>1) $str .= "$diff_minutes minutes ";
			else if ($diff_minutes>0) $str .= "$diff_hours minute ";
			if ($diff_seconds>1) $str .= "$diff_seconds secondes";
			else if ($diff_seconds>0) $str .= "$diff_seconds secondes";
			break;

		case "horaire":
			$str = ($diff_hours+($diff_days*24)+($diff_weeks*24*7)+($diff_year*24*7*365))."h ";
			if ($diff_minutes<10) $str .= "0";
			$str .= $diff_minutes."min ";
			if ($diff_seconds<10) $str .= "0";
			$str .= $diff_seconds."s";
			break;

		case "minutes":
			$str = $diff_minutes+($diff_hours*60)+($diff_days*60*24)+($diff_weeks*60*24*52)+($diff_year*60*24*365);
			break;

		case "iso8601":
			$str = "P${diff_years}Y${diff_weeks}W${diff_days}DT${diff_hours}H${diff_minutes}M${diff_seconds}S";
			break;

		case "ical":
			$str = "P${diff_years}Y${diff_weeks}W${diff_days}DT${diff_hours}H${diff_minutes}M${diff_seconds}S";  // mm chose que iso
			break;

		case "Y":
			$str = $diff_years;
			break;

		case "W":
			$str = $diff_weeks;
			break;

		case "D":
			$str = $diff_days;
			break;

		case "H":
			$str = $diff_hours;
			break;

		case "M":
			$str = $diff_minutes;
			break;

		case "Y":
			$str = $diff_years;
			break;

		case "S":
			$str = $diff_seconds;
			break;

		default:
			break;

	}

	return $str;

}


// Calculer la médiane d'un ensemble de nombres
function bigbrother_mediane(){

    $args = func_get_args();

    switch(func_num_args())
    {
        case 0:
            trigger_error('median() requires at least one parameter',E_USER_WARNING);
            return false;
            break;

        case 1:
            $args = array_pop($args);
            // fallthrough

        default:
            if(!is_array($args)) {
                trigger_error('median() requires a list of numbers to operate on or an array of numbers',E_USER_NOTICE);
                return false;
            }

            sort($args);

            $n = count($args);
            $h = intval($n / 2);

            if($n % 2 == 0) {
                $median = ($args[$h] + $args[$h-1]) / 2;
            } else {
                $median = $args[$h];
            }

            break;
    }

    return $median;

}


function get_jour($week,$daynb,$year,$type='timestamp'){
    $Jan1 = mktime(1,1,1,1,1,$year);
    $day_thursday = ($daynb <= 4) ? (4 - $daynb) : ($daynb-4);
    if($daynb <= 4)
    	$dayoffset = (11-date('w',$Jan1))%7-$day_thursday;
    else
    	$dayoffset = (11-date('w',$Jan1))%7+$day_thursday;
    $desiredday = strtotime(($week-1) . ' weeks '.$dayoffset.' days', $Jan1);
    if($type == 'mysql'){
    	$desiredday = date('Y-m-d H:i:s',$desiredday);
    }
    return $desiredday;
}
?>
