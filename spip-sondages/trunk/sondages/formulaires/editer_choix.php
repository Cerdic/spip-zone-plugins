<?php
/**
 * Plugin Spip-sondages
 * (c) 2012 Maïeul Rouquette d&#039;après Artego
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_choix_identifier_dist($id_choix='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_choix)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_choix_charger_dist($id_choix='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('choix',$id_choix,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_choix_verifier_dist($id_choix='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('choix',$id_choix, array('Ordre'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_choix_traiter_dist($id_choix='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('choix',$id_choix,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>