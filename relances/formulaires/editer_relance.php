<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_relance_charger_dist($id_relance='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('relance', $id_relance, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_relance_verifier_dist($id_relance='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('relance', $id_relance, array('titre'));
	return $erreurs;
}

function formulaires_editer_relance_traiter_dist($id_relance='new', $retour=''){
	return formulaires_editer_objet_traiter('relance', $id_relance, '', '', $retour, '');
}

?>
