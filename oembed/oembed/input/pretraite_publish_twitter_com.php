<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_pretraite_publish_twitter_com_dist($data_url, $args) {

	if (isset($args['url']) and strpos($args['url'],'#!/')!==false) {
		$url = str_replace('#!/', '', $args['url']);
		$data_url = parametre_url($data_url, 'url', $url, '&');
		$args['url'] = $url;
	}

	return $data_url;
}
