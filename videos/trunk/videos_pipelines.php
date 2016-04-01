<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Sauvegarde de configuration avec ieconfig
 * 
 * @pipeline ieconfig_metas
 * @param array $flux
 * @return array
 */
function videos_ieconfig_metas($table) {
	$table['videos']['titre'] = _T('paquet-videos:videos_nom');
	$table['videos']['icone'] = 'prive/themes/spip/images/videos-16.png';
	$table['videos']['metas_serialize'] = 'videos';
	return $table;
}

/**
 * Ajouter des js 
 * 
 * @pipeline insert_head
 * @param string $flux
 * @return string
 */
function videos_insert_head($flux) {
	include_spip('inc/config');
	$flux .= "\n<script type='text/javascript'>var CONFIG_WMODE = '" . lire_config('videos/wmode', 'opaque') . "';</script>\n";
	return $flux;
}

/**
 * Ajouter des css 
 * 
 * @pipeline insert_head_css
 * @param string $flux
 * @return string
 */
function videos_insert_head_css($flux) {
	include_spip('inc/config');
	$css = find_in_path('theme/css/videos.css');
	$flux .= "\n<link rel='stylesheet' href='" . direction_css($css) . "' type='text/css' media='all' />\n";
	return $flux;
}

/**
 * Ajouter la librairie JS html5media sur les pages
 * 
 * @link https://html5media.info/
 * @pipeline jquery_plugins
 * @param array $scripts
 * @return array
 */
function videos_jquery_plugins($scripts) {
	$scripts[] = "lib/html5media-1.1.8/api/html5media.min.js";
	return $scripts;
}

/**
 * Ajouter le formulaire d'ajout de vidéos au pied du formulaire d'ajout de document.
 * 
 * @pipeline formulaire_fond
 * @param array $flux
 * @return array
 */
function videos_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'joindre_document') {
		$videos = recuperer_fond(
			'prive/contenu/videos_affiche_boite',
			array(
				'objet' => $flux['args']['contexte']['objet'],
				'id_objet' => $flux['args']['contexte']['id_objet']
			)
		);
		// Injecter videos au dessus du formulaire joindre_document.
		$flux['data'] .= $videos;
	}
	return $flux;
}
