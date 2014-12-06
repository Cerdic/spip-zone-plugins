<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_input_pretraite_soundcloud_com_dist($data_url,$args){

	if (!isset($args['maxheight']) OR !$args['maxheight'])
		$data_url = parametre_url($data_url,"maxheight","81","&");

	return $data_url;
}