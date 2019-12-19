<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_pretraite_api_arte_tv_dist($data_url, $args) {

	if (preg_match(',(?:www\\.)?arte.tv/(\\w{2})/videos/([a-zA-Z0-9\\-]+)/.*?,', $args['url'], $regs)) {
		$data_url = $args['endpoint'] . $regs[1] . '/' . $regs[2];
	}

	return $data_url;
}
