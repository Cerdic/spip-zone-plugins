<?php
/**
 * Plugin sociaux
 * Licence GPL3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Inserer une CSS pour le contenu embed
 *
 * @param $head
 *
 * @return string
 */
function sociaux_insert_head_css($head) {
	include_spip('inc/config');
	if (lire_config('sociaux/css', 0)) {
		$head .= '<link rel="stylesheet" type="text/css" href="' . find_in_path('lib/socicon/style.css') . '" />' . "\n";
		$head .= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/sociaux.css') . '" />' . "\n";
	}

	return $head;
}

function sociaux_sociaux_lister($flux) {
	/**
	 * On reprend les éléments initiaux de la v1 du plugin :
	 * facebook, twitter, instagram, google-plus, blogger, pinterest, linkedin, youtube, rss, email, tripadvisor, vimeo, flickr
	 *
	 */
	if (isset($flux['data'])) {
		$flux['data']['facebook'] = 'Facebook';
		$flux['data']['twitter'] = 'Twitter';
		$flux['data']['instagram'] = 'Instagram';
		$flux['data']['googleplus'] = 'Google Plus';
		$flux['data']['blogger'] = 'Blogger';
		$flux['data']['pinterest'] = 'Pinterest';
		$flux['data']['linkedin'] = 'Linkedin';
		$flux['data']['youtube'] = 'Youtube';
		$flux['data']['rss'] = 'RSS';
		$flux['data']['mail'] = 'E-mail';
		$flux['data']['tripadvisor'] = 'TripAdvisor';
		$flux['data']['vimeo'] = 'Vimeo';
		$flux['data']['flickr'] = 'Flickr';
		$flux['data']['viber'] = 'Viber';
		$flux['data']['whatsapp'] = 'Whatsapp';
		$flux['data']['skype'] = 'Skype';
	}

	return $flux;
}

/**
 * Ajoute les scripts JS et CSS de saisies dans l'espace privé
 *
 * @param string $flux
 * @return string
 **/
function sociaux_header_prive($flux){
	$css = find_in_path('lib/socicon/style.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
	return $flux;
}

