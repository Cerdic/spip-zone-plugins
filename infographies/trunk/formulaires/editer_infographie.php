<?php
/**
 * Terraeco Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2013 - Distribué sous licence GNU/GPL
 *
 * Formulaire d'édition d'infographies
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_infographie_charger_dist($id_infographie='new', $retour='', $lier_trad=0, $config_fonc='infographies_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('infographie',$id_infographie,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_infographie_identifier_dist($id_infographie='new', $retour='', $lier_trad=0, $config_fonc='infographies_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_infographie),$lier_trad));
}

// Choix par defaut des options de presentation
function infographies_edit_config($row){
	return array();
}

function formulaires_editer_infographie_verifier_dist($id_infographie='new', $retour='', $lier_trad=0, $config_fonc='infographies_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('infographie',$id_infographie,array('titre'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_infographie_dist
function formulaires_editer_infographie_traiter_dist($id_infographie='new', $retour='', $lier_trad=0, $config_fonc='infographies_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('infographie',$id_infographie,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

?>
