<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_numero_charger_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('numero', $id_numero, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$valeurs['type'] = sql_getfetsel('type', 'spip_numeros_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_numero='.intval($id_numero) );
	return $valeurs;
}

function formulaires_editer_numero_verifier_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('numero', $id_numero, array() );
	return $erreurs;
}

function formulaires_editer_numero_traiter_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('numero', $id_numero, '', '', $retour, '');
}

?>
