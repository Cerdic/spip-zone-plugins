<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2013 - Distribue sous licence GNU/GPL
 * 
 * Formulaire d'edition d'un template de formulaire "Diogene"
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_diogene_charger_dist($id_diogene='new',$objet='article', $retour='',$config_fonc='', $row=array(), $hidden=''){
	$pipeline = pipeline('diogene_objets');
	$valeurs = array();
	if(is_array($pipeline) AND !isset($pipeline[$objet])){
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('diogene:erreur_objet_non_diogene',array('objet'=>$objet));
		return $valeurs;
	}else if(
		!is_numeric($id_diogene)
		&& is_array($pipeline)
		&& isset($pipeline[$objet]['diogene_max'])
		&& sql_countsel('spip_diogenes','type='.sql_quote($objet)) >= intval($pipeline[$objet]['diogene_max'])){
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('diogene:erreur_objet_diogene_max',array('objet'=>$objet,'max'=>$pipeline[$objet]['diogene_max']));
		return $valeurs;
	}

	$valeurs = formulaires_editer_objet_charger('diogene',$id_diogene,0,0,$retour,$config_fonc,$row,$hidden);
	if(empty($valeurs['objet']) OR (!in_array($valeurs['objet'],array('article','rubrique')) && !is_int($id_diogene)))
		$valeurs['objet'] = $objet;

	if(isset($valeurs['type'])){
		$valeurs['identifiant'] = $valeurs['type'];
		unset($valeurs['type']);
	}
	if(intval($valeurs['id_secteur']) && !$secteur_existe=sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.intval($valeurs['id_secteur'])))
		$valeurs['message_erreur'] = _T('diogene:erreur_secteur_diogene_inexistant');

	return $valeurs;
}

function formulaires_editer_diogene_verifier_dist($id_diogene='new',$objet='article', $retour='', $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('diogene',$id_diogene,array('titre','statut_auteur','identifiant'));
	$type = _request('identifiant');
	if($id_diogene = sql_getfetsel('id_diogene','spip_diogenes','type='.sql_quote($type).' AND id_diogene!='.intval($id_diogene)))
		$erreurs['identifiant'] = _T('diogene:erreur_identifiant_existant');

	return $erreurs;
}

function formulaires_editer_diogene_traiter_dist($id_diogene='new',$objet='article', $retour='', $config_fonc='', $row=array(), $hidden=''){
	/**
	 * On invalide le cache pour que les modifications sur les droits
	 * soient automatiquement prises en compte par les squelettes
	 */
	include_spip('inc/invalideur');
	suivre_invalideur(1);
	return formulaires_editer_objet_traiter('diogene',$id_diogene,0,'',$retour,$config_fonc,$row,$hidden);
}

?>
