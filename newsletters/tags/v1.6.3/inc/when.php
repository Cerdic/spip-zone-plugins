<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recuperer la prochaine occurence d'une repetition
 * (occurence après la date $prev si fournie)
 *
 * @param string $date_start
 * @param string $rule
 * @param string $prev
 * @return string
 */
function when_rule_to_next_date($date_start,$rule,$prev=''){
	include_spip("lib/when/src/When");
	include_spip("lib/when/src/Valid");

	try {
		$r = new When\When();
		$r->startDate(new DateTime($date_start))->rrule($rule);
		if (!$prev) {
			$prev = $date_start;
		}
		while ($next=$r->getNextOccurrence(new DateTime($prev))
			AND $prev AND strtotime($prev)>=strtotime($next->format("Y-m-d H:i:s")));

		if ($next){
			return $next->format("Y-m-d H:i:s");
		}
		return '';
	}
	catch (Exception $e){
		return $e->getMessage();
	}
}

/**
 * Afficher en texte clair une regle de repetition
 * @param string $rule
 * @param string $sep
 * @return string
 */
function when_rule_to_texte($rule, $sep=", "){

	$r = when_rule_ro_array($rule);
	$texte = array();

	// FREQ + INTERVAL
	// array('SECONDLY', 'MINUTELY', 'HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY');
	if (isset($r['FREQ'])){
		$interval = (isset($r['INTERVAL'])?$r['INTERVAL']:1);
		$chaine = "when:info_freq_".$r['FREQ'];
		$texte[] = singulier_ou_pluriel($interval,$chaine,$chaine."_nb");
	}

	// TODO : "BYSETPOS"

	if (isset($r['BYWEEKNO']))
		$texte[] = singulier_ou_pluriel($r['BYWEEKNO'],"when:info_byweek_1","when:info_byweek_nb");

	if (isset($r['WKST']))
		$texte[] = when_wkst_to_texte($r['WKST']);

	if (isset($r['BYDAY']))
		$texte[] = when_byday_to_texte($r['BYDAY']);

	if (isset($r['BYMONTHDAY']))
		$texte[] = when_bymonthday_to_texte($r['BYMONTHDAY']);

	if (isset($r['BYMONTH']))
		$texte[] = when_bymonth_to_texte($r['BYMONTH']);

	if (isset($r['BYYEARDAY']))
		$texte[] = when_byyearday_to_texte($r['BYYEARDAY']);

	if (isset($r['COUNT']) AND $r['COUNT']>1)
		$texte[] = singulier_ou_pluriel($r['COUNT'],"when:info_1_fois","when:info_nb_fois");

	if (isset($r['UNTIL']) AND $r['UNTIL']){
		$texte[] = _T('when:info_until_date',array('date'=>affdate(date('Y-m-d H:i:s',strtotime($r['UNTIL'])))));
	}

	$texte = array_filter($texte);

	return implode($sep,$texte);
}

/**
 * Transformer une rule texte en tableau constitue de chacun de ses arguments
 * @param string $rule_string
 * @return array
 */
function when_rule_ro_array($rule_string){
	$licites = array("INTERVAL","FREQ","BYWEEKNO","WKST","BYDAY","BYMONTHDAY","BYMONTH","BYYEARDAY","COUNT","UNTIL","BYSETPOS");
	$r = array();
	// strip off a trailing semi-colon
	$rule_string = trim($rule_string, ";");
	$parts = explode(";", $rule_string);

	foreach($parts as $part) {
		list($rule, $param) = explode("=", $part);
		if (in_array($rule,$licites))
			$r[$rule] = $param;
	}

	return $r;
}

/**
 * WKST en texte clair
 * @param string $wkst
 * @return string
 */
function when_wkst_to_texte($wkst){
	$day2n = array('SU'=>1, 'MO'=>2, 'TU'=>3, 'WE'=>4, 'TH'=>5, 'FR'=>6, 'SA'=>7);

	return "("._T('when:info_wkst_day',array('day'=>_T("date_jour_".$day2n[$wkst]))).")";
}

/**
 * BYDAY en texte clair
 * @param string $bydays
 * @return string
 */
function when_byday_to_texte($bydays){
	$day2n = array('SU'=>1, 'MO'=>2, 'TU'=>3, 'WE'=>4, 'TH'=>5, 'FR'=>6, 'SA'=>7);
	$texte = array();
	$bydays = explode(',',$bydays);
	foreach ($bydays as $byday){
		$day = substr($byday,-2);
		$day = _T("date_jour_".$day2n[$day]);
		$by = intval($byday);
		if ($by){
			if ($by<0) $by = "moins_".(-$by);
			$texte[] = _T('when:info_byday_'.$by.'_day',array('day'=>$day));
		}
		else {
			$texte[] = _T('when:info_byday_day',array('day'=>$day));
		}
	}
	return implode(", ",$texte);
}

/**
 * BYMONTHDAY en texte clair
 * @param string $bydays
 * @return string
 */
function when_bymonthday_to_texte($bydays){
	return when_byXday_to_texte($bydays, 'month');
}

/**
 * BYYEARDAY en texte clair
 * @param $bydays
 * @return string
 */
function when_byyearday_to_texte($bydays){
	return when_byXday_to_texte($bydays, 'year');
}

/**
 * function support pour BYMONTHDAY/BYYEARDAY
 * @param string $bydays
 * @param string $x
 * @return string
 */
function when_byXday_to_texte($bydays, $x='month'){
	$texte = array();
	$bydays = explode(',',$bydays);
	foreach ($bydays as $byday){
		if ($byday<0){
			if ($byday==2)
				$texte[] = _T('when:info_by'.$x.'day_moins_2');
			else
				$texte[] = singulier_ou_pluriel($byday,'when:info_by'.$x.'day_moins_1','when:info_by'.$x.'day_moins_nb');
		}
		else {
			$texte[] = singulier_ou_pluriel($byday,'when:info_by'.$x.'day_1','when:info_by'.$x.'day_nb');
		}
	}
	return implode(", ",$texte);
}

/**
 * BYMONTH en texte clair
 * @param $bymonths
 * @return mixed|string
 */
function when_bymonth_to_texte($bymonths){
	$months = array();
	$bymonths = explode(",",$bymonths);
	foreach ($bymonths as $bymonth){
		$months[] = _T('date_mois_'.$bymonth);
	}
	$nb = count($months);
	$months = implode(', ',$months);
	if ($nb==1) return _T('when:info_bymonth_1',array('month'=>$months));
	elseif ($nb>1) return _T('when:info_bymonth_nb',array('months'=>$months));
	return "";
}