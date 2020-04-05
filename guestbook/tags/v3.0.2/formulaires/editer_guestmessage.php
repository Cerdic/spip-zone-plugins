<?php
/**
 * Plugin Guestbook
 * (c) 2013 Yohann Prigent (potter64), Stephane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_guestmessage_identifier_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_guestmessage)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_guestmessage_charger_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('guestmessage',$id_guestmessage,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_guestmessage_verifier_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('guestmessage',$id_guestmessage);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_guestmessage_traiter_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('guestmessage',$id_guestmessage,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>