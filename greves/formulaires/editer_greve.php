<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
function formulaires_editer_greve_charger_dist($id_greve='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('greve', $id_greve, '', '', $retour, '');
	if ($valeurs['titre']==''){
		$valeurs['titre']==_T('greves:texte_nouvelle_greve');
	}
	$valeurs['debut'] = separer_date_heure($valeurs['debut']);
	if ($valeurs['debut']!=''){
		$valeurs['date_debut'] = affdate($valeurs['debut'][0],'d/m/Y');
		$valeurs['heure_debut'] = substr($valeurs['debut'][1],0,5);
	}
	
	$valeurs['fin'] = separer_date_heure($valeurs['fin']);
	if ($valeurs['fin']!=''){
		$valeurs['date_fin'] = affdate($valeurs['fin'][0],'d/m/Y');
		$valeurs['heure_fin'] = substr($valeurs['fin'][1],0,5);
	}
	return $valeurs;
}
function formulaires_editer_greve_verifier_dist($id_greve='new', $retour=''){

	return $erreurs;
}
function formulaires_editer_greve_traiter_dist($id_greve='new', $retour=''){
	$valeurs['titre']	= _request('titre');
	$valeurs['texte']	= _request('texte');
	$valeurs['debut']	= convertir_date(_request('date_debut')) . ' ' . _request('heure_debut').':00';
	$valeurs['fin']		= convertir_date(_request('date_fin')) . ' ' . _request('heure_fin').':00';
	if ($id_greve!='new'){
		sql_updateq('spip_greves',$valeurs,'id_greve='.intval($id_greve));
	}
	else{
		$id_greve = sql_insertq	('spip_greves',$valeurs);
	}
	return array('redirect'=>generer_url_ecrire('greves'));
}

function separer_date_heure($date){
	return explode(' ',$date);
}

function convertir_date($date){
	$tableau = explode('/',$date);
	return $tableau[2]. '-' . $tableau[1] . '-' . $tableau[0];
}
?>
