<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_pretraite_www_youtube_com_dist($data_url, $args) {

	$data_url = parametre_url($data_url, 'scheme', 'https', '&');

	return $data_url;
}
