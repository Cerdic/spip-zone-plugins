<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_produit_charger_dist($id_produit='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('produit', $id_produit, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_produit_verifier_dist($id_produit='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('produit', $id_produit, array('nom'));
	return $erreurs;
}

function formulaires_editer_produit_traiter_dist($id_produit='new', $retour=''){
	return formulaires_editer_objet_traiter('produit', $id_produit, '', '', $retour, '');
}

?>
