<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_modifier_grigri_charger_dist($objet, $id_objet = 0){

	$table  = table_objet_sql($objet);
	$id     = id_table_objet($objet);
	$grigri = sql_getfetsel('grigri', $table, "$id=" .intval($id_objet) );

	$valeurs = array(
		"grigri"  => $grigri,
	);
	return $valeurs;
}

function formulaires_modifier_grigri_verifier_dist($objet, $id_objet = 0){
	$erreurs = array();
	return $erreurs;
}

function formulaires_modifier_grigri_traiter_dist($objet, $id_objet = 0){
	$grigri   = _request('grigri');
	include_spip('action/editer_objet');
	$set = array ( 'grigri'    => str_replace(' ', '_', $grigri),);
	objet_modifier($objet, $id_objet, $set);

	$retour = array();

	return $retour;
}
