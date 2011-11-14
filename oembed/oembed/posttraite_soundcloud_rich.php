<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_posttraite_soundcloud_rich_dist($data){

	$data['type'] = 'sound';
	$data['html'] = preg_replace(",width=['\"][0-9]+['\"],i",'width="100%"',$data['html']);

	return $data;
}