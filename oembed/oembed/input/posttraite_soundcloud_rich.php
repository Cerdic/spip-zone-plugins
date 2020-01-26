<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_soundcloud_rich_dist($data) {

	$data['media'] = 'audio';
	$data['html'] = preg_replace(",width=['\"][0-9]+['\"],i", 'width="100%"', $data['html']);

	if (!isset($data['thumbnail_url'])) {
		$data['thumbnail_url'] = find_in_path('oembed/input/vignettes/soundcloud.png');
	} else {
		$data['thumbnail_url'] = preg_replace(',^http://,Uims', 'https://', $data['thumbnail_url']);
	}

	return $data;
}
