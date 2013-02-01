<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 * 
 * Formulaire d'edition d'un template de formulaire "Diogene"
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_diogene_charger_dist($id_diogene='new',$objet='article', $retour='',$config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('diogene',$id_diogene,0,0,$retour,$config_fonc,$row,$hidden);
	if(empty($valeurs['objet']) OR (!in_array($valeurs['objet'],array('article','rubrique')) && !is_int($id_diogene))){
		$valeurs['objet'] = $objet;
	}
	return $valeurs;
}

function formulaires_editer_diogene_verifier_dist($id_diogene='new',$objet='article', $retour='', $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('diogene',$id_diogene,array('titre','statut_auteur','type'));
	$type = _request('type');
	if($id_diogene = sql_getfetsel('id_diogene','spip_diogenes','type='.sql_quote($type).' AND id_diogene!='.intval($id_diogene))){
		$erreurs['type'] = _T('diogene:erreur_identifiant_existant');
	}
	return $erreurs;
}

function formulaires_editer_diogene_traiter_dist($id_diogene='new',$objet='article', $retour='', $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('diogene',$id_diogene,0,'',$retour,$config_fonc,$row,$hidden);
	/**
	 * On invalide le cache pour que les modifications sur les droits
	 * soient automatiquement prises en compte par les squelettes
	 */
	include_spip('inc/invalideur');
	suivre_invalideur(1);
	$res['message_ok'] = _T('diogene:message_diogene_update');
	$res['editable'] = true;
	return $res;
}

?>
