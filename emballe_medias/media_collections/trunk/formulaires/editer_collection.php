<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012 kent1
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_collection_identifier_dist($id_collection='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_collection), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_collection_charger_dist($id_collection='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('collection',$id_collection,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	include_spip('inc/config');
	/**
	 * Récupération des valeurs de ces deux champs sinon on utilise les valeurs par défaut
	 */
	$valeurs['type_collection'] = _request('type_collection') ? _request('type_collection') :  $valeurs['type_collection'];
	$valeurs['genre'] = _request('genre') ? _request('genre') : $valeurs['genre'];
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_collection_verifier_dist($id_collection='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('collection',$id_collection, array('titre'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_collection_traiter_dist($id_collection='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('collection',$id_collection,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	if($res['id_collection'] != $id_collection){
		$res['redirect'] = $retour ? parametre_url($retour,'id_collection',$res['id_collection']) : parametre_url(self(),'id_collection',$res['id_collection']);
	}
	$statut = sql_getfetsel('statut','spip_collections','id_collection='.intval($res['id_collection']));
	if($statut == 'poubelle'){
		$res['redirect'] = $retour ? parametre_url($retour,'id_collection','') : parametre_url(self(),'id_collection','');
	}
	return $res;
}

?>