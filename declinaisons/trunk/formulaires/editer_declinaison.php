<?php
/**
 * Plugin Déclinaisons Produit
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_declinaison_identifier_dist($id_declinaison='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_declinaison)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_declinaison_charger_dist($id_declinaison='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('declinaison',$id_declinaison,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
    $valeurs['_hidden']='<input type="hidden" value="publie" name="statut"/>';
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_declinaison_verifier_dist($id_declinaison='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('declinaison',$id_declinaison, array('titre'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_declinaison_traiter_dist($id_declinaison='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('declinaison',$id_declinaison,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>