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

function formulaires_editer_spipimmo_annonce_charger_dist($id_annonce='new', $retour=''){
	$valeurs = formulaires_editer_objet_charger('spipimmo_annonces', $id_annonce, '', '', $retour, '');
	return $valeurs;
}

function formulaires_editer_spipimmo_annonce_verifier_dist($id_annonce='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('spipimmo_annonces', $id_annonce, array('civilite', 'id_proprio', 'id_negociateur', 'nom', 'prenom', 'adresse', 'code_postal', 'ville', 'tel_fixe'));
	return $erreurs;
}

function formulaires_editer_spipimmo_annonce_traiter_dist($id_annonce='new', $retour=''){
	return formulaires_editer_objet_traiter('spipimmo_annonces', $id_annonce, '', '', $retour, '');
}
?>
