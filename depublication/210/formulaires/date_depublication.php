<?php

/*
 * Plugin Depublication
 * (c) 2010 Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */
 
 

function formulaires_date_depublication_charger_dist($objet, $id_objet=0) {
	// pas d'element, pas de traduction

	if (!$objet or !($id_objet = intval($id_objet))) {
		return false;
	}
	

	$table = table_objet_sql($objet);
	$_id_objet = id_table_objet($objet);
	$date_depublication = $statut_depublication = '';
	$res = sql_fetsel('date_depublication, statut_depublication', $table, "$_id_objet = ". sql_quote($id_objet));
	if ($res and is_array($res)) {
		$date_depublication   = $res['date_depublication'];
		$statut_depublication = $res['statut_depublication'];
		// ne pas s'embêter avec les années 1970...
		if ($date_depublication == '0000-00-00 00:00:00') {
			$date_depublication = '';
		} else {
			$date = strtotime($date_depublication);
		}
	}
		
	$contexte = array(
		'editable'=>true,
		'jour' => ($date_depublication ? date('j', $date) : ''),
		'mois' => ($date_depublication ? date('n', $date) : ''),
		'annee' => ($date_depublication ? date('Y', $date) : ''),
		'heure' => ($date_depublication ? date('G', $date) : ''),
		'minute' => ($date_depublication ? intval(date('s', $date)) : ''),
		'statut' => ($statut_depublication ? $statut_depublication : ''),
	);
		
	
	return $contexte;
}


function formulaires_date_depublication_traiter_dist($objet, $id_objet=0) {
	$jour   = intval(_request('jour'));
	$mois   = intval(_request('mois'));
	$annee  = intval(_request('annee'));
	$heure  = intval(_request('heure'));
	$minute = intval(_request('minute'));
	$statut = _request('statut');
	
	$submit  = _request('save');
	$annuler = _request('annuler');
	
	$date = date('Y-m-d H:i:s', mktime($heure, $minute, 0, $mois, $jour, $annee));
	
	if ($annuler) {
		$statut = '';
		$date = '0000-00-00 00:00:00';
		set_request('jour','');
		set_request('mois','');
		set_request('annee','');
		set_request('heure','');
		set_request('minute','');
		set_request('statut','');
	}
	
	include_spip('inc/modifier');
	modifier_contenu($objet, $id_objet, array('invalideur' => "id='$objet/$id_objet'"), array(
		'date_depublication' => $date, 
		'statut_depublication' => $statut
	));

	if ($annuler) {
		$message = _T('depublication:date_depublication_annulee');
	} else {
		$message = _T('depublication:date_depublication_mise_a_jour_a', array('date'=> affdate($date)));
	}
	return array(
		'message_ok' => $message,
		'editable' => true
	);
}

?>
