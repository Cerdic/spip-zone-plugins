<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1
 * Licence GNU/GPL
 * 
 * Formulaire d'ajout de medias à une collection
 * 
 * Permet d'ajouter un ou plusieurs medias à une collection
 * Permet également de réorganiser une collection via glisser/déposer
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_ajouter_media_collection_identifier_dist($id_collection='new', $retour=''){
	return serialize(array(intval($id_collection)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_ajouter_media_collection_charger_dist($id_collection='new', $retour=''){
	include_spip('inc/autoriser');
	if(intval($id_collection) && ($collection = sql_fetsel('*','spip_collections','id_collection='.intval($id_collection))) && autoriser('modifier','collection',intval($id_collection))){
		$valeurs['id_collection'] = $collection['id_collection'];
		if($collection['genre'] == 'mixed'){
			$valeurs['document_media'] = '.*';
		}else{
			$valeurs['document_media'] = $collection['genre'];
		}
		$valeurs['id_secteur'] = sql_getfetsel('id_secteur','spip_diogenes','objet="emballe_media"');
		$valeurs['editable'] = true;
	}else{
		$valeurs['editable'] = false;
	}
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_ajouter_media_collection_verifier_dist($id_collection='new', $retour=''){
	$erreurs = array();
	$elements_ajouter = _request('medias_collection_ajouter') ? _request('medias_collection_ajouter') : array();
	$elements_dissocier = _request('medias_collection_modifier') ? _request('medias_collection_modifier') : array();
	if(((count($elements_ajouter) + count($elements_dissocier)) == 0) && (_request('rang_modifier') == 0 || !_request('rang_modifier')))
		$erreurs['message_erreur'] = _T('collection:erreur_selectionner_un_media');
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_ajouter_media_collection_traiter_dist($id_collection='new', $retour=''){
	$elements_ajouter = _request('medias_collection_ajouter') ? _request('medias_collection_ajouter') : array();
	$elements_modifier = _request('medias_collection_modifier') ? _request('medias_collection_modifier') : array();
	$autoriser_lier = autoriser('lierobjet','collection',$id_collection) ? true : false;
	$autoriser_modifier = autoriser('modifier','collection',$id_collection) ? true : false;
	
	$compte_elements_ajouter = $compte_elements_modifier = $compte_elements_dissocier = 0;
	
	if(intval($id_collection) && ($id_collection = sql_getfetsel('id_collection','spip_collections','id_collection='.intval($id_collection))) && $autoriser_lier){
		$id_collection = $id_collection;
	}else{
		return $res['message_erreur'] = _T('collection:erreur_collection_non_existante');
	}

	if((count($elements_ajouter) > 0) || (count($elements_modifier) > 0)){
		include_spip('action/editer_liens');
		/**
		 * On récupère le dernier rang possible.
		 * On récupère en fait le compte d'élément de la collection auquel on ajoute 1 à chaque élément 
		 * ajouté à la collection pour constituer le nouveau rang
		 */
		$dernier_rang = sql_countsel('spip_collections_liens','id_collection='.intval($id_collection));
		foreach($elements_ajouter as $id_article){
			if ($autoriser_lier) {
				$dernier_rang = $dernier_rang + 1;
				objet_associer(array('collection' => $id_collection), array('article' => $id_article),array('id_auteur' =>$GLOBALS['visiteur_session']['id_auteur']?$GLOBALS['visiteur_session']['id_auteur']:0,'rang'=>$dernier_rang));
				$compte_elements_ajouter++;
			}
		}
		foreach($elements_modifier as $id_article){
			if (_request('supprimer_elements_collection') && $autoriser_modifier) {
				objet_dissocier(array('collection' => $id_collection), array('article' => $id_article));
				$compte_elements_dissocier++;
			}
		}
		if($compte_elements_ajouter > 0){
			$res['message_ok'] = singulier_ou_pluriel($compte_elements_ajouter,'collection:message_nombre_ajoute','collection:message_nombre_ajoutes');
		}
		if($compte_elements_dissocier > 0){
			spip_log('reorganiser','test');
			if(strlen($res['message_ok']) > 0)
				$res['message_ok'] .= '<br />';
			else
				$res['message_ok'] = '';
			$res['message_ok'] .= singulier_ou_pluriel($compte_elements_dissocier,'collection:message_nombre_dissocie','collection:message_nombre_dissocies');
		}
	}

	$rangs = is_array(_request('rang')) ? _request('rang') : array();
	foreach ($rangs as $rang=>$id_article){
		if(!_request('supprimer_elements_collection') || !in_array($id_article,$elements_modifier)){
			$rang = $rang + 1;
			$compte_elements_modifier++;
			$ok = sql_updateq('spip_collections_liens',array('rang' => intval($rang)),"objet='article' AND id_objet = ".intval($id_article));
		}
	}
	if($compte_elements_dissocier > 0){
		$organiser = charger_fonction('collection_organiser_rangs','inc');
		$organiser($id_collection);
	}
	
	if($compte_elements_modifier > 0){
		if(strlen($res['message_ok']) > 0)
			$res['message_ok'] .= '<br />';
		else
			$res['message_ok'] = '';
		$res['message_ok'] .= _T('collection:message_collection_reorganisee');
	}
	
	if(($compte_elements_ajouter > 0) || ($compte_elements_dissocier > 0) || ($compte_elements_modifier > 0)){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
	return $res;

}


?>