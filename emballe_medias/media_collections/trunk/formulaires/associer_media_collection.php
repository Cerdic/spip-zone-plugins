<?php
/**
 * Plugin Collection (ou albums)
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012-2013 - Distribue sous licence GNU/GPL
 *
 * Formulaire d'association d'un media à une collection
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');
include_spip('inc/config');

/**
 *
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function formulaires_associer_media_collection_charger_dist($id_objet){
	$valeurs = array(
		'editable'=>true, # Peut on utiliser le formulaire
		'id_objet' => $id_objet, # Quel est l'id_objet à utiliser
		'_collections' => array() # La liste des collections auxquelles le media est déjà lié
	);
	
	/**
	 * As t on au moins une collection à laquelle on peut ajouter ce medias
	 */
	$id_collection = sql_getfetsel('id_collection','spip_collections as collection LEFT JOIN spip_auteurs_liens as lien ON collection.id_collection = lien.id_objet AND lien.objet="collection"','collection.statut = "publie" AND lien.id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
	if(!$id_collection){
		$valeurs['editable'] = false;
		return $valeurs;
	}
	/**
	 * Le secteur des medias
	 */
	$secteur_medias = sql_getfetsel('id_secteur','spip_diogenes','objet="emballe_media"');
	
	/**
	 * On n'est pas auteur ou l'$id_objet passé n'est pas numérique
	 * On rend le formulaire non éditable
	 */
	if(!isset($GLOBALS['visiteur_session']['statut']) OR !is_numeric(intval($id_objet))){
		$valeurs['editable'] = false;
	}
	/**
	 * On vérifie que l'on est bien sur un media et non un article X
	 */
	else if(
		(sql_getfetsel('id_secteur','spip_articles','id_article = '.intval($id_objet)) != $secteur_medias)
		OR (sql_countsel('spip_documents_liens','objet="article" AND id_objet = '.intval($id_objet)) != 1)){
		$valeurs['editable'] = false;
		return $valeurs;
	}
	
	/**
	 * On rempli l'array "collections" avec l'ensemble des collections où ce media est associé
	 * 
	 * On récupère également le media du document
	 */
	else {
		$deja_collections = sql_select('id_collection','spip_collections_liens','objet="article" AND id_objet='.intval($id_objet));
		while($collection = sql_fetch($deja_collections)){
			$valeurs['_collections'][] = $collection['id_collection']; 
		}
		$valeurs['_genres'] = array('mixed');
		$valeurs['_genres'][] = sql_getfetsel('doc.media','spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document','lien.objet="article" AND lien.id_objet='.intval($id_objet));
	}
	return $valeurs;
}

function formulaires_associer_media_collection_verifier_dist($id_objet){
	$erreurs = array();
	return $erreurs;
}

function formulaires_associer_media_collection_traiter_dist($id_objet){
	$res = array('message_ok'=>' ');
	
	$collection_ajoutee = false;
	if(intval(_request('id_collection')) && ($id_collection = sql_getfetsel('id_collection','spip_collections','id_collection='.intval(_request('id_collection')))) && autoriser('lierobjet','collection',intval(_request('id_collection')))){
		$id_collection = $id_collection;
	}else if(!_request('id_collection')){
		unset($res['message_ok']);
		return $res;
	}
	
	include_spip('action/editer_liens');
	
	if (autoriser('lierobjet', 'collection', $id_collection)) {
		$rang = sql_countsel('spip_collections_liens','id_collection='.intval($id_collection));
		$association = objet_associer(array('collection' => $id_collection), array('article' => $id_objet),array('id_auteur' => $GLOBALS['visiteur_session']['id_auteur']?$GLOBALS['visiteur_session']['id_auteur']:0,'rang'=>$rang+1));
	}
	
	if(!$association){
		return $res['message_erreur'] = _T('collection:erreur_association_collection');
	}else{
		$organiser = charger_fonction('collection_organiser_rangs','inc');
		$organiser($id_collection);
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
	
	$autoclose = (lire_config('collections/mediabox') == "on" OR !defined('_DIR_PLUGIN_MEDIABOX')) ? '' : "<script type='text/javascript'>if (window.jQuery) jQuery.modalboxclose();</script>";
	if (!isset($res['message_erreur'])){
		$res['message_ok'] = $autoclose;
		$res['editable'] = false;
	}

	if ($res['message_ok'])
		$res['message_ok'].= '<script type="text/javascript">if (window.jQuery) jQuery(".info-collections_liees").ajaxReload();</script>';
	
	return $res;
}
?>