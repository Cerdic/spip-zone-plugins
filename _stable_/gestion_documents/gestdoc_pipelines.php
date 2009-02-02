<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */


function gestdoc_post_edition($flux){
	// si on ajoute un document, mettre son statut a jour
	if($flux['args']['operation']=='ajouter_document'){
		include_spip('action/editer_document');
		// mettre a jour le statut si necessaire
		instituer_document($flux['args']['id_objet']);
	}
	// si on institue un objet, mettre ses documents lies a jour
	if(
	  ($flux['args']['operation']=='instituer'
	  OR isset($flux['data']['statut']))
	  AND $flux['args']['table']!=='spip_documents'){
	  include_spip('base/abstract_sql');
	  $type = objet_type($flux['args']['table']);
	  $id = $flux['args']['id_objet'];
	  $docs = array_map('reset',sql_allfetsel('id_document','spip_documents_liens','id_objet='.intval($id).' AND objet='.sql_quote($type)));
		include_spip('action/editer_document');
	  foreach($docs as $id_document)
			// mettre a jour le statut si necessaire
			instituer_document($id_document);
	}
	return $flux;
}

function gestdoc_affiche_gauche($flux){
	/*
	if ($flux['args']['exec']=='articles_edit'){
		if (!$id_article=intval($flux['args']['id_article'])){
			$id_article = 0-$GLOBALS['visiteur_session']['id_auteur'];
		}
		$flux['data'] .= recuperer_fond('prive/editer/colonne_document',array('objet'=>'article','id_objet'=>$id_article));
	}
	*/
	return $flux;
}