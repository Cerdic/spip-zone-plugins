<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_adresse_charger_dist($id_adresse='new', $objet='', $id_objet='', $retour='', $type=''){
	$valeurs = formulaires_editer_objet_charger('adresse', $id_adresse, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$valeurs['type'] = $type;
	return $valeurs;
}

function formulaires_editer_adresse_verifier_dist($id_adresse='new', $objet='', $id_objet='', $retour='', $type=''){
	$erreurs = formulaires_editer_objet_verifier('adresse', $id_adresse);
	return $erreurs;
}

function formulaires_editer_adresse_traiter_dist($id_adresse='new', $objet='', $id_objet='', $retour='', $type=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('adresse', $id_adresse, '', '', $retour, '');
}

?>
