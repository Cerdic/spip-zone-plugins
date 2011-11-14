<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_posttraite_soundcloud_rich_dist($data){

	$data['type'] = 'sound';

	return $data;
}