<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_spipimmo_negociateur_charger_dist($id_negociateur='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('spipimmo_negociateurs', $id_negociateur, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_spipimmo_negociateur_verifier_dist($id_negociateur='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('spipimmo_negociateurs', $id_negociateur, array('civilite', 'nom', 'prenom', 'adresse', 'code_postal', 'ville', 'tel_fixe'));
	return $erreurs;
}

function formulaires_editer_spipimmo_negociateur_traiter_dist($id_negociateur='new', $retour=''){
	return formulaires_editer_objet_traiter('spipimmo_negociateurs', $id_negociateur, '', '', $retour, '');
}
?>
