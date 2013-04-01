<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012 kent1
 * Licence GNU/GPL
 * 
 * Formulaire de création rapide de collection
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_creer_collection_rapide_charger_dist($retour=''){
	$valeurs = array();
	$valeurs['titre'] = '';
	$valeurs['editable'] = autoriser('creer','collection');
	return $valeurs;
}

function formulaires_creer_collection_rapide_verifier_dist($retour=''){
	$erreurs = array();
	if(!_request('titre'))
		$erreurs['titre'] = _T('info_obligatoire');
	return $erreurs;
}

function formulaires_creer_collection_rapide_traiter_dist($retour=''){
	$res = formulaires_editer_objet_traiter('collection','oui','',$lier_trad,$retour,$config_fonc,$row,$hidden);
	if(intval($res['id_collection']) && is_numeric($res['id_collection'])){
		include_spip('action/editer_objet');
		$err = objet_instituer('collection', $res['id_collection'], array('statut' => 'publie'));
		if(!$err && !$retour)
			$res['redirect'] = self();
	}
	return $res;
}


?>