<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_issuu_dist($data, $url) {
	// le thumbnail génère un bloc avec un arrière plan tout moche
	$data['thumbnail_url'] = '';
	return $data;
}
