<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_numero_charger_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('numero', $id_numero, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	return $valeurs;
}

function formulaires_editer_numero_verifier_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('numero', $id_numero);
	return $erreurs;
}

function formulaires_editer_numero_traiter_dist($id_numero='new', $objet='', $id_objet='', $retour=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('numero', $id_numero, '', '', $retour, '');
}

?>
