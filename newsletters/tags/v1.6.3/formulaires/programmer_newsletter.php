<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/when');
include_spip('inc/newsletters');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_programmer_newsletter_identifier_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_newsletter)));
}


/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_programmer_newsletter_charger_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	$charger = charger_fonction("charger","formulaires/editer_newsletter");
	$valeurs = $charger($id_newsletter, $retour, $lier_trad, $config_fonc, $row, $hidden);
	unset($valeurs['id_newsletter']);


	list($date_start,$rule) = newsletter_ics_to_date_rule($valeurs['recurrence']);
	$date_start = ($date_start?strtotime($date_start):time());
	$valeurs['date_debut'] = date('d/m/Y',$date_start);
	$valeurs['date_debut_heure'] = date('H:i',$date_start);

	$r = when_rule_ro_array($rule);
	$valeurs = formulaires_programmer_newsletter_charger_rule($r, $valeurs);

	$lists = charger_fonction('lists','newsletter');
	$valeurs['_listes_dispo'] = $lists();
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_programmer_newsletter_verifier_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	set_request('baked',0);
	$verifier = charger_fonction("verifier","formulaires/editer_newsletter");
	$erreurs = $verifier($id_newsletter, $retour, $lier_trad, $config_fonc, $row, $hidden);

	if (!_request('date_debut'))
		$erreurs['date_debut'] = _T('info_obligatoire');
	else {
		list($annee, $mois, $jour,,,) = recup_date(_request('date_debut'));
		if (!$d=mktime(0,0,0,$mois,$jour,$annee))
			$erreurs['date_debut'] = _T('programmernewsletter:erreur_date_incorrecte');
		else {
			$d2 = date("Y-m-d ",$d)._request('date_debut_heure');
			$d2 = strtotime($d2);
			if ($d2<$d OR $d2>$d+24*3600)
				$erreurs['date_debut'] = _T('programmernewsletter:erreur_heure_incorrecte');
		}
	}

	if (!in_array(_request('frequence'),array('daily','weekly','monthly','yearly')))
		$erreurs['frequence'] = _T('info_obligatoire');

	if (_request('has_end')=='count' AND !_request('count'))
		$erreurs['has_end'] = _T('info_obligatoire');

	if (_request('has_end')=='until'){
		if (!_request('until'))
			$erreurs['until'] = _T('info_obligatoire');
		else {
			list($annee, $mois, $jour,,,) = recup_date(_request('until'));
			if (!mktime(23,59,59,$mois,$jour,$annee))
				$erreurs['until'] = _T('programmernewsletter:erreur_date_incorrecte');
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_programmer_newsletter_traiter_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	set_request('baked',0);

	// date debut
	list($annee, $mois, $jour,,,) = recup_date(_request('date_debut'));
	$date_debut = mktime(0,0,0,$mois,$jour,$annee);
	$date_debut = date('Y-m-d ',$date_debut)._request('date_debut_heure');
	$date_debut = date('Y-m-d H:i:s',strtotime($date_debut));

	$recurrence = formulaires_programmer_newsletter_traiter_rule();

	$ics = newsletter_date_rule_to_ics($date_debut, $recurrence);
	set_request('recurrence',$ics);

	// prochaine occurence dans "date" : la premiere que l'on trouve a partir d'aujourd'hui inclus
	// date_redac contient ensuite la date de la precedente occurence
	$d = when_rule_to_next_date($date_debut,$recurrence,date('Y-m-d H:i:s',strtotime("-1 day")));
	if (!$d)
		$d = "0001-01-01 00:00:00";
	set_request('date',$d);

	// statut prog
	set_request('statut','prog'); // toujours en 'prog'

	$traiter = charger_fonction("traiter","formulaires/editer_newsletter");
	$res = $traiter($id_newsletter, $retour, $lier_trad, $config_fonc, $row, $hidden);

	// mettre a jour le cron : on supprime la tache et on reprogramme tous les crons
	include_spip("inc/genie");
	sql_delete("spip_jobs","fonction=".sql_quote('newsletters_programmees'));
	genie_queue_watch_dist();

	if (isset($res['message_ok']))
		$res['message_ok'] .= "<br />".when_rule_to_texte($recurrence);

	return $res;
}


/**
 * Transformer la rule texte en saisie
 *
 * @param string $r
 * @param array $valeurs
 * @return array
 */
function formulaires_programmer_newsletter_charger_rule($r, &$valeurs){

	$valeurs['frequence'] = 'monthly';
	$valeurs['daily_interval'] = 1;
	$valeurs['weekly_interval'] = 1;
	$valeurs['monthly_interval'] = 1;
	$valeurs['yearly_interval'] = 1;
	$valeurs['byweekday'] = array(1,2,3,4,5,6,7);
	$valeurs['has_end'] = 'no';
	$valeurs['until'] = '';
	$valeurs['count'] = '';

	// FREQ + INTERVAL
	if (in_array($r['FREQ'],array('DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY'))){
		$valeurs['frequence'] = strtolower($r['FREQ']);
		$interval = (isset($r['INTERVAL'])?$r['INTERVAL']:1);
		$valeurs[$valeurs['frequence']."_interval"] = $interval;
	}

	// TODO : "BYSETPOS"
	// pas de saisie pour le moment

	// TODO : "BYWEEKNO"
	// pas de saisie pour le moment

	// "WKST"
	// pas de saisie
	if (isset($r['BYDAY'])
	  AND $valeurs['frequence']=="weekly"){
		$day2n = array('SU'=>1, 'MO'=>2, 'TU'=>3, 'WE'=>4, 'TH'=>5, 'FR'=>6, 'SA'=>7);
		$valeurs['byweekday'] = array();
		$bydays = explode(',',$r['BYDAY']);
		foreach ($bydays as $byday){
			$day = substr($byday,-2);
			$valeurs['byweekday'][] = $day2n[$day];
		}
	}

	// "BYMONTHDAY"
	// pas de saisie : c'est le jour de la date de depart qui fixe le monthday en freq monthly

	// "BYMONTH"
	// pas de saisie : c'est le jour de la date de depart qui fixe le monthday en freq monthly

	// "BYYEARDAY"
	// pas de saisie : c'est le jour de la date de depart qui fixe le monthday en freq monthly

	if (isset($r['COUNT']) AND $r['COUNT']){
		$valeurs['has_end'] = 'count';
		$valeurs['count'] = intval($r['COUNT']);
	}

	if (isset($r['UNTIL']) AND $r['UNTIL']){
		$valeurs['has_end'] = 'until';
		$valeurs['until'] = date('d/m/Y',strtotime($r['UNTIL']));
	}

	return $valeurs;
}

/**
 * Recuperer la saisie de recurrence et la transformer en rule ics texte
 * @return string
 */
function formulaires_programmer_newsletter_traiter_rule(){
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
		list($annee, $mois, $jour,,,) = recup_date(_request('until'));
		$date = mktime(23,59,59,$mois,$jour,$annee);
		$rule[] = "UNTIL=".gmdate("Ymd\THis\Z",$date);
	}

	$rule = implode(';',$rule);
	return $rule;
}

function newsletters_list_subscribers($list){
	$subscribers = charger_fonction('subscribers','newsletter');
	return $subscribers(array($list),array('count'=>true));
}

?>