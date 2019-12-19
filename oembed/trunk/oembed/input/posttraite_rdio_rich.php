<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_rdio_rich_dist($data) {

	$data['media'] = 'audio';

	return $data;
}
