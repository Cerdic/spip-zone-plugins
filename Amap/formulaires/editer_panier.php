<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_panier_charger_dist($id_panier='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('panier', $id_panier, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_panier_verifier_dist($id_panier='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('panier', $id_panier, array('nom'));
	return $erreurs;
}

function formulaires_editer_panier_traiter_dist($id_panier='new', $retour=''){
	return formulaires_editer_objet_traiter('panier', $id_panier, '', '', $retour, '');
}

?>
