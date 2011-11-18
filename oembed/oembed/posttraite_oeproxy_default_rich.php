<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_posttraite_oeproxy_default_rich_dist($data, $url){

	$data['html'] = "<a href='$url' class='spip_out oe-reference'>$url</a>
	<blockquote class='spip oe-cite' cite='$url'>".$data['html']."</blockquote>";

	return $data;
}