<?php
/**
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * @param string $flux
 * 		Le contexte du pipeline
 * @return string $flux
 * 		Le contexte du pipeline modifié
 */
function photospip_header_prive($flux) {
	$flux .= '
			<link rel="stylesheet" href="' . direction_css(find_in_path(_DIR_LIB_IMGAREASELECT . 'css/imgareaselect-animated.css')) . '" type="text/css" media="all" />';
	$flux .= '
			<link rel="stylesheet" href="' . direction_css(find_in_path('css/photospip_prive.css')) . '" type="text/css" media="all" />';
	return $flux;
}

/**
 * Insertion dans le pipeline jqueryui_forcer (Plugin jQuery UI)
 * On ajoute dans les plugins de jQuery UI le chargement des sliders dans
 * l'espace privé
 */
function photospip_jqueryui_forcer($plugins) {
	if (test_espace_prive())
		$plugins[] = 'jquery.ui.slider';
	return $plugins;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute dans les plugins jquery chargés par SPIP "ImgAreaSelect"
 * dans l'espace privé
 */
function photospip_jquery_plugins($plugins) {
	if (test_espace_prive())
		$plugins[] = _DIR_LIB_IMGAREASELECT . 'scripts/jquery.imgareaselect.js';
	return $plugins;
}

/**
 * Insertion dans le pipeline document_desc_actions (Plugin Mediathèque)
 * Ajouter le lien vers l'édition de l'image
 *
 * @param string $flux
 * @return string
 */
function photospip_document_desc_actions($flux = '') {
	$id_document = $flux['args']['id_document'];
	$infos = sql_fetsel('distant,extension', 'spip_documents', 'id_document=' . intval($id_document));
	if (($infos['distant'] == 'non') && in_array($infos['extension'], array('jpg', 'png', 'gif'))) {
		$redirect = self();
		$url = parametre_url(generer_url_ecrire('image_edit', 'id_document=' . intval($id_document)), 'redirect', $redirect);
		$texte = _T('photospip:lien_editer_image');
		if ($flux['args']['position'] == 'galerie') {
			$flux['data'] .= "[<a href='$url'>$texte</a>]";
		} else {
			$flux['data'] .= "<span class='sep'> | </span><a href='$url'>$texte</a>";
		}
	}
	return $flux;
}
?>