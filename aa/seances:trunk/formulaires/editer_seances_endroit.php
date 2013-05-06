<?php

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_seances_endroit_charger_dist($id_endroit='new', $retour=''){
	$valeurs = array();
	$valeurs = formulaires_editer_objet_charger('seances_endroit', $id_endroit, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_seances_endroit_verifier_dist($id_endroit='new', $retour=''){
	$erreurs = array();
	$nom_endroit = trim(_request('nom_endroit'));
	if (empty($nom_endroit))
		$erreurs['nom_endroit'] = _T('seances:erreur_nom_endroit_vide');
	return $erreurs;
}

function formulaires_editer_seances_endroit_traiter_dist($id_endroit='new', $retour=''){
	return formulaires_editer_objet_traiter('seances_endroit',$id_endroit, '', '', $retour, '');
}
?>