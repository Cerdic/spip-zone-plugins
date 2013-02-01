<?php 
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Action de récupération des documents depuis l'article original
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_diogene_recup_doc_trad_dist(){
	$id_article = _request('arg');
	if(!is_numeric($id_article)){
		return;
	}
	
	$id_trad = sql_getfetsel('id_trad','spip_articles','id_article='.intval($id_article));
	
	if(!is_numeric($id_trad)){
		return;
	}
	
	diogene_recuperer_docs_trad($id_article,$id_trad);
	
	$redirect = _request('redirect');
	if(!$redirect){
		include_spip('diogene_fonctions');
		$redirect = generer_url_publier($id_article);
	}
	
	include_spip('inc/invalideur');
	suivre_invalideur("0",true);
	
	include_spip('inc/headers');
	redirige_par_entete(str_replace('&amp;','&',$redirect));
	
}

function diogene_recuperer_docs_trad($id_article,$id_trad){
	/**
	 * On lui ajoute automatiquement les documents de l'article original
	 */
	$docs = sql_select('*','spip_documents_liens','objet="article" AND id_objet='.intval($id_trad));
	while($doc = sql_fetch($docs)){
		sql_insertq("spip_documents_liens", array('id_objet' => $id_article, 'objet' => 'article', 'id_document' => $doc['id_document'], 'vu' => $doc['vu']));
		pipeline('post_edition',
			array(
				'args' => array(
					'operation' => 'lier_document',
					'table' => 'spip_documents',
					'id_objet' => $doc['id_document'],
					'objet' => 'article',
					'id' => $id_article
				),
				'data' => null
			)
		);
	}
}
?>