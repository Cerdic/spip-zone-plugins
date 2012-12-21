<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_programmer_newsletter_charger_dist(){
	$valeurs = array(
		'date_debut' => '',
		'frequence' => 'monthly',
		'daily_interval' => 1,
		'weekly_interval' => 1,
		'byweekday' => array(1,2,3,4,5,6,7),
		'monthly_interval' => 1,
		'yearly_interval' => 1,
		'has_end' => 'no',
		'until' => '',
		'count' => '',
	);


	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_programmer_newsletter_verifier_dist(){
	$erreurs = array();

	if (!_request('date_debut'))
		$erreurs['date_debut'] = _T('info_obligatoire');

	if (!in_array(_request('frequence'),array('daily','weekly','monthly','yearly')))
		$erreurs['frequence'] = _T('info_obligatoire');

	if (_request('has_end')=='count' AND !_request('count'))
		$erreurs['has_end'] = _T('info_obligatoire');

	if (_request('has_end')=='until' AND !_request('until'))
		$erreurs['has_end'] = _T('info_obligatoire');

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_programmer_newsletter_traiter_dist(){

	// FREQ=DAILY;INTERVAL=10;COUNT=5
	$rule = array();

	$freq = _request('frequence');
	$rule[] = "FREQ=".strtoupper($freq);

	if ($i = intval(_request($freq."_interval"))
		AND $i>1)
		$rule[] = "INTERVAL=".$i;

	if ($freq=="weekly"
	  AND $byweekday = _request('byweekday')
	  AND count($byweekday)<7){
		$day2n = array('SU'=>1, 'MO'=>2, 'TU'=>3, 'WE'=>4, 'TH'=>5, 'FR'=>6, 'SA'=>7);
		$n2day = array_flip($day2n);
		$d = array();
		foreach($byweekday as $n){
			$d[] = $n2day[$n];
		}
		$rule[] = "BYDAY=".implode(",",$d);
	}

	if (_request('has_end')=='count')
		$rule[] = "COUNT=".intval(_request('count'));

	if (_request('has_end')=='until'){
		list($annee, $mois, $jour, $heures, $minutes, $secondes) = recup_date(_request('until'));
		$date = mktime($heures,$minutes,$secondes,$mois,$jour,$annee);
		$rule[] = "UNTIL=".date("Ymd\THis\Z",$date);
	}

	$rule = implode(';',$rule);
	include_spip("inc/when");
	$texte = when_rule_to_texte($rule);

	$res = array('message_ok'=>"$rule<br />$texte");

	return $res;
}


?>