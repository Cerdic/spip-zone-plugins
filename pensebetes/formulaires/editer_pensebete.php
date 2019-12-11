<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_pensebete_charger_dist($id_pensebete='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('pensebete',$id_pensebete,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	if (empty($valeurs['id_donneur']))
		$valeurs['id_donneur']=$GLOBALS['visiteur_session']['id_auteur'];
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_pensebete_identifier_dist($id_pensebete='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_pensebete)));
}

function formulaires_editer_pensebete_verifier_dist($id_pensebete='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('pensebete', $id_pensebete);
}

function formulaires_editer_pensebete_traiter_dist($id_pensebete='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	set_request('id_auteur','');// éviter que l'auteur soit associé au pense-bête.
	return formulaires_editer_objet_traiter('pensebete',$id_pensebete,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

