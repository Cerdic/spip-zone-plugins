<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_instagram_rich_dist($data) {

	#var_dump($data);
	// Instagram est un fournisseur de photo, on ne veut pas de leur mouchard iframe+js sociaux
	$data['media'] = 'image';
	if (isset($data['thumbnail_url'])
		and isset($data['thumbnail_width'])
		and isset($data['thumbnail_height'])) {
		$data['type'] = 'photo';
		$data['url'] = $data['thumbnail_url'];
		$data['width'] = $data['thumbnail_width'];
		$data['height'] = $data['thumbnail_height'];
	}

	return $data;
}
