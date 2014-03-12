<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_input_posttraite_yfrog_dist($data){

	$data['type']='photo';
	return $data;
}