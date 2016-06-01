<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_oeproxy_default_rich_dist($data, $url) {

	#$data['html'] = "<blockquote class='spip oe-cite' cite='$url'>".$data['html']."</blockquote>";

	return $data;
}
