<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_chat_charger_dist($id_chat='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('chat',$id_chat,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_chat_identifier_dist($id_chat='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_chat)));
}

function formulaires_editer_chat_verifier_dist($id_chat='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('chat', $id_chat);
}

function formulaires_editer_chat_traiter_dist($id_chat='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('chat',$id_chat,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

