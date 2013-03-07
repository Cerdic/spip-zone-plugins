<?php
/**
 * Media auteurs
 *
 * Copyright (c) 2012
 * Yohann Prigent
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 * Pour plus de details voir le fichier COPYING.txt.
 *  
 **/
function media_aut_affiche_gauche($flux){
	include_spip('inc/presentation');
	if ($flux['args']['exec'] == 'document_edit'){
		$flux['data'] .= 
		debut_cadre_relief('',true,'', _T('media_aut:lie_doc_a_auteur')) .
		recuperer_fond('inclure/document_auteur', array('id_document' => _request('id_document'))) .
		fin_cadre_relief(true);
	}
	if ($flux['args']['exec'] == 'auteur'){
		$flux['data'] .= 
		debut_cadre_relief('',true,'', _T('media_aut:liaisons_auteurs_doc')) .
		recuperer_fond('inclure/page_auteur_mgauche', array('id_auteur' => _request('id_auteur'))) .
		fin_cadre_relief(true);
	}
	return $flux;
}
function media_aut_header_prive ($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('inclure/media_aut.css').'" type="text/css" media="all" />';
	return $flux;
}
?>