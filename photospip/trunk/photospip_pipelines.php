<?php
function photospip_header_prive($flux){
	$flux .= '
			<link rel="stylesheet" href="'.direction_css(find_in_path(_DIR_LIB_IMGAREASELECT.'css/imgareaselect-animated.css')).'" type="text/css" media="all" />';
	$flux .= '
			<link rel="stylesheet" href="'.direction_css(find_in_path('css/photospip_prive.css')).'" type="text/css" media="all" />';
	return $flux;
}

function photospip_jqueryui_forcer($plugins){
	$plugins[] = 'jquery.ui.slider';
	$plugins[] = 'jquery.ui.tabs';
	spip_log($plugins,'test');
	return $plugins;
}

function photospip_jquery_plugins($plugins){
	$plugins[] = _DIR_LIB_IMGAREASELECT.'scripts/jquery.imgareaselect.js';
	return $plugins;
}


/**
 * Ajouter le lien vers l'édition de l'image
 *
 * @param array $flux
 * @return array
 */
function photospip_document_desc_actions($flux){
	$id_document = $flux['args']['id_document'];
	$infos = sql_fetsel('distant,extension','spip_documents','id_document='.intval($id_document));
	if(($infos['distant'] == 'non') && in_array($infos['extension'],array('jpg','png','gif'))){
		$redirect = self();
		$url = parametre_url(generer_url_ecrire('image_edit','id_document='.intval($id_document)),'redirect',$redirect);
		$texte = _T('photospip:lien_editer_image');
		if($flux['args']['position'] == 'galerie'){
			$flux['data'] .= "[<a href='$url'>$texte</a>]";
		}else{
			$flux['data'] .= "<span class='sep'> | </span><a href='$url'>$texte</a>";
		}
	}
	return $flux;
}
?>