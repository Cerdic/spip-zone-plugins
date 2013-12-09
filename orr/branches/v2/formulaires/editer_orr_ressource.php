<?php
/**
 * Plugin ORR
 * (c) 2012 tofulm
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_orr_ressource_identifier_dist($id_orr_ressource='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_orr_ressource)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_orr_ressource_charger_dist($id_orr_ressource='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('orr_ressource',$id_orr_ressource,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_orr_ressource_verifier_dist($id_orr_ressource='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('orr_ressource',$id_orr_ressource, array('orr_ressource_nom'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_orr_ressource_traiter_dist($id_orr_ressource='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('orr_ressource',$id_orr_ressource,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
