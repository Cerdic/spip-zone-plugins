<?php

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_seance_charger_dist($id_seance='new', $id_article, $retour='', $duplicate=''){
	$valeurs = array();
	if ($duplicate and ($duplicate == intval($duplicate)))
		/* $valeurs = sql_fetsel(array('date_seance','id_endroit','remarque_seance'),'spip_seances','id_seance='.$duplicate); */
		$valeurs = formulaires_editer_objet_charger('seance', $duplicate, '', '', $retour, '');
	else
		$valeurs = formulaires_editer_objet_charger('seance', $id_seance, '','', $retour, '');
	
	if ($valeurs['date_seance']){
		$valeurs['jour'] = jour($valeurs['date_seance']);
		$valeurs['mois'] = mois($valeurs['date_seance']);
		$valeurs['annee'] = annee($valeurs['date_seance']);
		$valeurs['heure'] = heures($valeurs['date_seance']);
		$valeurs['minutes'] = minutes($valeurs['date_seance']);
	} else {
		$valeurs['jour'] = date('d');
		$valeurs['mois'] = date('m');
		$valeurs['annee'] = date('Y');
		$valeurs['heure'] = date('H');
		$valeurs['minutes'] = substr('0'.floor(date('i')/5)*5,-2);
	}
	$valeurs['id_article'] = $id_article;
	$valeurs['id_seance'] = $id_seance;
	$valeurs['duplicate'] = $duplicate;
	return $valeurs; 
}

function formulaires_editer_seance_verifier_dist($id_seance='new', $id_article, $retour='', $duplicate=''){
	$erreurs = array();
	$date_seance = _request('annee').'-'._request('mois').'-'._request('jour').' '._request('heure').':'._request('minutes').':00';
	set_request('date_seance',$date_seance);
	$erreurs = formulaires_editer_objet_verifier('seance', $id_seance, array('id_endroit','date_seance'));
	
	if (!checkdate(_request('mois'), _request('jour'), _request('annee')))
		$erreurs['date_seance'] = _T('seances:date_incorrecte');
	return $erreurs;
}

function formulaires_editer_seance_traiter_dist($id_seance='new', $id_article, $retour='',$duplicate=''){
	$date_seance = _request('annee').'-'._request('mois').'-'._request('jour').' '._request('heure').':'._request('minutes').':00';
	set_request('date_seance',$date_seance);
	return formulaires_editer_objet_traiter('seance', $id_seance, '','', $retour, '');
}
?>