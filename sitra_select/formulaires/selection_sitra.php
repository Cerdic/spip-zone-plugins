<?php
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_selection_sitra_charger_dist($id_article){
	$valeurs = array();
	$valeurs['id_article'] = $id_article;
	$ret = sql_fetsel('*', 'spip_sitra_select_articles', 'id_article='.$id_article);
	if (!$ret){
		$valeurs['id_selection'] = '';
		$valeurs['id_categorie'] = '';
		$valeurs['id_critere'] = '';
		$valeurs['noisette'] = '';
		$valeurs['tri'] = '';
		$valeurs['sens_tri'] = '';
		$valeurs['extra'] = '';
	} else {
		$valeurs['id_selection'] = $ret['id_selection'];
		$valeurs['id_categorie'] = $ret['id_categorie'];
		$valeurs['id_critere'] = $ret['id_critere'];
		$valeurs['noisette'] = $ret['noisette'];
		$valeurs['tri'] = $ret['tri'];
		$valeurs['sens_tri'] = $ret['sens_tri'];
		$valeurs['extra'] = $ret['extra'];
	}
	return $valeurs; 
}

function formulaires_selection_sitra_verifier_dist($id_article){
	$erreurs = array();
	return $erreurs;
}

function formulaires_selection_sitra_traiter_dist($id_article){
	$erreurs = array();
	
	$data = array();
	
	$champs = array('id_selection','id_categorie','id_critere','noisette','tri','sens_tri','extra');
	$tout_vide = true;
	foreach($champs as $champ){
		if (_request($champ))
			$tout_vide = false;
		$data[$champ] = _request($champ);
	}

			
	// update ou insert
	
	$n = sql_countsel('spip_sitra_select_articles','id_article='.$id_article);
	
	if (!$n and !$tout_vide){
		$data['id_article'] = $id_article;
		sql_insertq('spip_sitra_select_articles',$data);
		$message = _T('sitra_select:enregistrement_creation_ok');
	} else {
		if (!$tout_vide){
			sql_updateq('spip_sitra_select_articles',$data,'id_article='.$id_article);
			$message = _T('sitra_select:enregistrement_mise_a_jour_ok');
		} else {
			sql_delete('spip_sitra_select_articles','id_article='.$id_article);
			$message = _T('sitra_select:enregistrement_suppression_ok');
		}
	}
	$erreurs['message_ok'] = $message;
	
	return $erreurs;
}
?>