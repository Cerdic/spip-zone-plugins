<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_encart_charger_dist($id_encart='new', $id_article='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('id_encart', $id_encart, '', '', $retour, '');
	$valeurs['id_article'] = $id_article;
	$valeurs['maintenant'] = date('Y-m-d H:i:s');
	return $valeurs;
}


function formulaires_editer_encart_verifier_dist($id_encart='new', $id_article='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('id_encart', $id_encart);
	if (!encarts_date_picker_to_date(_request('date'))) {
		$erreurs['date'] = _L('Mauvais format de date !');
	}
	return $erreurs;
}


function formulaires_editer_encarts_traiter_dist($id_encart='new', $id_article='', $retour=''){
	set_request('date', encarts_date_picker_to_date(_request('date')));
	
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('id_encart', $id_encart, '', '', $retour, '');
}


function encarts_date_picker_to_date($datePicker, $heurePicker = '00:00'){
	// $datePicker : jj/mm/yyyy
	if (!$date = recup_date($datePicker . ' ' . $heurePicker . ':00')
	OR !($date = mktime($date[3],$date[4],0,$date[1],$date[2],$date[0]))) {
	  // mauvais format de date
	  return false;
	}

	return date("Y-m-d H:i:s",$date);
}
?>
