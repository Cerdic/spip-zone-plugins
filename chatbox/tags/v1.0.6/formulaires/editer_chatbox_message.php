<?php
/**
 * Plugin Chatbox
 * (c) 2013 g0uZ
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_chatbox_message_identifier_dist($id_chatbox_message='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_chatbox_message)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_chatbox_message_charger_dist($id_chatbox_message='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('chatbox_message',$id_chatbox_message,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_chatbox_message_verifier_dist($id_chatbox_message='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('chatbox_message',$id_chatbox_message, array('message'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_chatbox_message_traiter_dist($id_chatbox_message='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('chatbox_message',$id_chatbox_message,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>