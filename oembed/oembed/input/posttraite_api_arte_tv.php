<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_api_arte_tv_dist($data) {

	// récupérer l'image dans la meta og:image de la page source
	if ($res = recuperer_url($data['oembed_url_source'], array('taille_max'=>16384))
	  and $res['page']) {
		$metas = extraire_balises($res['page'], 'meta');
		$src = '';
		$has_image = false;
		foreach ($metas as $meta) {
			$property = extraire_attribut($meta, 'property');
			if ($property == 'og:image') {
				$src = extraire_attribut($meta, 'content');
			}
			if ($src) {
				break;
			}
		}
		if ($src) {
			$data['thumbnail_url'] = $src;
		}
	}
	// nettoyer l'iframe envoyée par arte.tv & modifier son src pour utiliser la v5 du player qui prend en charge l'autoplay
	if ($iframe = extraire_balise($data['html'], 'iframe')) {
		$iframe_cor = vider_attribut($iframe, 'height');
		$iframe_cor = vider_attribut($iframe_cor, 'scrolling');
		$iframe_cor = vider_attribut($iframe_cor, 'style');
		$iframe_cor = inserer_attribut($iframe_cor, 'src', str_replace('/v3/', '/v5/', extraire_attribut($iframe_cor, 'src')));
		$data['html'] = str_replace($data['html'], $iframe, $iframe_cor);
	}	

	return $data;
}
