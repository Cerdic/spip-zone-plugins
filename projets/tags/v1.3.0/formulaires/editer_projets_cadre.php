<?php
/**
 * Plugin Projets
 *
 * @plugin  Projets
 * @license GPL (c) 2009-2017
 * @author  Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Projets\EditerProjetsCadre
 **/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_projets_cadre_identifier_dist($id_projets_cadre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_projets_cadre)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_projets_cadre_charger_dist($id_projets_cadre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('projets_cadre',$id_projets_cadre,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_projets_cadre_verifier_dist($id_projets_cadre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('projets_cadre',$id_projets_cadre);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_projets_cadre_traiter_dist($id_projets_cadre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('projets_cadre',$id_projets_cadre,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}

