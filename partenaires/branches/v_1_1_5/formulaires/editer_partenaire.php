<?php
/**
 * Plugin Partenaires
 * (c) 2013 Teddy Payet
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_partenaire_identifier_dist($id_partenaire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_partenaire)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_partenaire_charger_dist($id_partenaire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('partenaire',$id_partenaire,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_partenaire_verifier_dist($id_partenaire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('partenaire',$id_partenaire, array('nom'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_partenaire_traiter_dist($id_partenaire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('partenaire',$id_partenaire,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>