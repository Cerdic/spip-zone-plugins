<?php
/**
 * Terraeco Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2013 - Distribué sous licence GNU/GPL
 *
 * Formulaire d'édition de jeux de données
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_infographies_donnee_charger_dist($id_infographies_donnee='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_donnees_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('infographies_donnee',$id_infographies_donnee,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_infographies_donnee_identifier_dist($id_infographies_donnee='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_donnees_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_infographies_donnee),$associer_objet));
}

// Choix par defaut des options de presentation
function infographies_donnees_edit_config($row){
	return array();
}

function formulaires_editer_infographies_donnee_verifier_dist($id_infographies_donnee='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_donnees_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('infographies_donnee',$id_infographies_donnee,array());
	return $erreurs;
}

function formulaires_editer_infographies_donnee_traiter_dist($id_infographies_donnee='new', $retour='',$associer_objet='', $lier_trad=0, $config_fonc='infographies_donnees_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('infographies_donnee',$id_infographies_donnee,0,$lier_trad,$retour,$config_fonc,$row,$hidden);

	return $res;
}

?>
