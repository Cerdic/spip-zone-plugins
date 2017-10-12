<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_twitter_dist($data) {

	$data['html'] = trim(preg_replace(',<script[^>]*></script>,i', '', $data['html']));

	// verifier l'URL du tweet si on peut trouver une image dans les og:image
	// pour afficher en tete de card
	if ($res = recuperer_url($data['url'], array('taille_max'=>16384))
	  and $res['page']) {
		$metas = extraire_balises($res['page'], 'meta');
		$src = '';
		$has_image = false;
		foreach ($metas as $meta) {
			$property = extraire_attribut($meta, 'property');
			if ($property == 'og:image') {
				$src = extraire_attribut($meta, 'content');
			}
			if ($property == 'og:image:user_generated' and extraire_attribut($meta, 'content')==='true') {
				$has_image = true;
			}
			if ($has_image and $src) {
				break;
			}
		}
		if ($has_image and $src) {
			$src = str_replace(':large', ':small', $src);
			$data['html'] = "<img src='$src' class='thumbnail p' />" . $data['html'];
		}
	}

	return $data;
}
