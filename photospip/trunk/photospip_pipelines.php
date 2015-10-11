<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2015 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * @param string $flux
 * 		Le contexte du pipeline
 * @return string $flux
 * 		Le contexte du pipeline modifié
 */
function photospip_header_prive($flux) {
	include_spip('inc/plugin'); // pour spip_version_compare
	$flux .= '
			<link rel="stylesheet" href="' . direction_css(find_in_path(_DIR_LIB_IMGAREASELECT . 'distfiles/css/imgareaselect-animated.css')) . '" type="text/css" media="all" />';
	if (spip_version_compare($GLOBALS['spip_version_branche'], '3.1-alpha', '<'))
		$flux .= '
			<link rel="stylesheet" href="' . direction_css(find_in_path("javascript/jQuery_ui_spinner/ui.spinner.css")) . '" type="text/css" media="all" />';
	return $flux;
}

/**
 * Insertion dans le pipeline jqueryui_forcer (Plugin jQuery UI)
 * On ajoute dans les plugins de jQuery UI le chargement des sliders dans
 * l'espace privé
 */
function photospip_jqueryui_plugins($plugins) {
	if (test_espace_prive()){
		include_spip('inc/plugin'); // pour spip_version_compare
		$plugins[] = 'jquery.ui.slider';
		if (spip_version_compare($GLOBALS['spip_version_branche'], '3.1-alpha', '>='))
			$plugins[] = 'jquery.ui.spinner';
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute dans les plugins jquery chargés par SPIP "ImgAreaSelect"
 * dans l'espace privé
 */
function photospip_jquery_plugins($plugins) {
	if (test_espace_prive()){
		include_spip('inc/plugin'); // pour spip_version_compare
		$plugins[] = _DIR_LIB_IMGAREASELECT . 'jquery.imgareaselect.dev.js';
		if (version_compare($GLOBALS['spip_version_branche'], '3.1-alpha', '<'))
			$plugins[] = find_in_path("javascript/jQuery_ui_spinner/ui.spinner.js");
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline document_desc_actions (Plugin Mediathèque)
 * Ajouter le lien vers l'édition de l'image
 *
 * @param array $flux
 * @return array $flux
 */
function photospip_document_desc_actions($flux) {
	$id_document = $flux['args']['id_document'];
	$infos = sql_fetsel('distant,id_vignette,extension', 'spip_documents', 'id_document=' . intval($id_document));
	if (($infos['distant'] == 'non') && in_array($infos['extension'], array('jpg', 'png', 'gif'))) {
		$redirect = self();
		$url_modif = parametre_url(generer_url_ecrire('image_edit', 'id_document=' . intval($id_document)), 'retour', $redirect);
		$texte_modif = _T('photospip:lien_editer_image');
		$url_vignette = parametre_url(parametre_url(generer_url_ecrire('image_edit','id_document='.intval($id_document)),'mode','vignette'), 'retour', $redirect);
		$texte_vignette = _T('photospip:lien_editer_vignette');
		if ($flux['args']['position'] == 'galerie') {
			$flux['data'] .= "[<a href='$url_modif'>$texte_modif</a>] [<a href='$url_vignette'>$texte_vignette</a>]";
		} else {
			$flux['data'] .= "<span class='sep'> | </span><a href='$url_modif' target='_blank' class='editbox'>$texte_modif</a><span class='sep'> | </span><a href='$url_vignette' target='_blank' class='editbox'>$texte_vignette</a>";
		}
	}else if($id_vignette = sql_getfetsel('id_document','spip_documents','id_document='.intval($infos['id_vignette']))){
		$url_vignette = parametre_url(generer_url_ecrire('image_edit','id_document='.intval($id_document)),'mode','vignette');
		$texte_vignette = _T('photospip:lien_editer_vignette');
		if ($flux['args']['position'] == 'galerie') {
			$flux['data'] .= "[<a href='$url_vignette'>$texte_vignette</a>]";
		} else {
			$flux['data'] .= "<span class='sep'> | </span><a href='$url_vignette' target='_blank' class='editbox'>$texte_vignette</a>";
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * On vérifie le contenu du formulaire de configuration 
 * 
 * @param array $flux
 * @return array $flux
 */
function photospip_formulaire_verifier($flux){
	if ($flux['args']['form']=='configurer_photospip'){
		if(count(_request('resultats')) == 0){
			$flux['data']['resultats'] = _T('photospip:erreur_selectionner_au_moins_une_valeur');
		}
	}	
	return $flux;
}


/**
 * Insertion dans le pipeline boite_infos (SPIP)
 * On ajoute quelques boutons sur les pages de modification de documents
 * 
 * @param array $flux
 * @return array $flux
 */
function photospip_boite_infos($flux){
	if ($flux['args']['type']=='document'){
		$id_document = $flux['args']['id'];
		include_spip('inc/presentation');
		if((_request('mode') != 'vignette') && ($document = sql_fetsel('extension,id_vignette','spip_documents','id_document='.intval($id_document)))){
			if(_request('exec') != 'document_edit')
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_modifier_document'), parametre_url(generer_url_ecrire('document_edit','id_document='.$id_document),'redirect',self()), 'photospip', 'edit-16.png',false);
			if((_request('exec') != 'image_edit') && in_array($document['extension'], array('jpg', 'png', 'gif')))
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_editer_image'), parametre_url(generer_url_ecrire('image_edit','id_document='.$id_document),'redirect',self()), 'photospip', 'edit-16.png',false);
			if($document['id_vignette'] && intval($document['id_vignette']) > 0){
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_editer_vignette'), parametre_url(generer_url_ecrire('image_edit','id_document='.$id_document),'mode','vignette'), 'photospip', 'edit-16.png',false);
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_supprimer_vignette_document'), generer_action_auteur('supprimer_document',$document['id_vignette'],parametre_url(self(),'supprimer_vignette','oui')), 'photospip', 'del-16.png',false);
				$flux['data'] .= recuperer_fond('prive/photospip_vignette',array('id_document'=>intval($id_document)));
			}else if(in_array($document['extension'],array('gif','png','jpg'))){
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_creer_vignette'), parametre_url(generer_url_ecrire('image_edit','id_document='.$id_document),'mode','vignette'), 'photospip', 'new.png',false);
			}
		}elseif((_request('mode') == 'vignette') && ($extension = sql_getfetsel('extension','spip_documents','id_document='.intval($id_document)))){
			$flux['data'] .= icone_horizontale(_T('photospip:bouton_modifier_document'), generer_url_ecrire('document_edit','id_document='.$id_document), 'photospip', 'edit-16.png',false);
			if(in_array($extension, array('jpg', 'png', 'gif')))
				$flux['data'] .= icone_horizontale(_T('photospip:bouton_editer_image'), generer_url_ecrire('image_edit','id_document='.$id_document), 'photospip', 'edit-16.png',false);
		}
	}
	return $flux;
}
?>