<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_oeproxy_imdb_rich_dist($data, $url) {
	include_spip('inc/filtres');
	$data['html'] = filtrer('image_reduire', $data['html'], 75, 100);

	return $data;
}
