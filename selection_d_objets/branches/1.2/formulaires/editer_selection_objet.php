<?php
/**
 * Plugin Selection d&#039;objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_selection_objet_identifier_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_selection_objet)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_selection_objet_charger_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('selection_objet',$id_selection_objet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_selection_objet_verifier_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('selection_objet',$id_selection_objet);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_selection_objet_traiter_dist($id_selection_objet='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('selection_objet',$id_selection_objet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>