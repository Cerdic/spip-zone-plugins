<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_cat_variante_charger_dist($id_cat_variante='new', $id_article='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('cat_variante', $id_cat_variante, '', '', $retour, '');
	$valeurs['id_article'] = $id_article;
	$valeurs['maintenant'] = date('Y-m-d H:i:s');
	return $valeurs;
}


function formulaires_editer_cat_variante_verifier_dist($id_cat_variante='new', $id_article='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('cat_variante', $id_cat_variante);
	if (!cat_date_picker_to_date(_request('date'))) {
		$erreurs['date'] = _L('Mauvais format de date !');
	}
	if (!cat_date_picker_to_date(_request('date_redac'))) {
		$erreurs['date_redac'] = _L('Mauvais format de date de r&eacute;daction !');
	}
	return $erreurs;
}


function formulaires_editer_cat_variante_traiter_dist($id_cat_variante='new', $id_article='', $retour=''){
	set_request('date', cat_date_picker_to_date(_request('date')));
	set_request('date_redac', cat_date_picker_to_date(_request('date_redac')));
	
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('cat_variante', $id_cat_variante, '', '', $retour, '');
}


function cat_date_picker_to_date($datePicker, $heurePicker = '00:00'){
	// $datePicker : jj/mm/yyyy
	if (!$date = recup_date($datePicker . ' ' . $heurePicker . ':00')
	OR !($date = mktime($date[3],$date[4],0,$date[1],$date[2],$date[0]))) {
	  // mauvais format de date
	  return false;
	}

	return date("Y-m-d H:i:s",$date);
}
?>
