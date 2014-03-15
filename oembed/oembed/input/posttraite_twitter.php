<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_input_posttraite_twitter_dist($data){

	$data['html'] = trim(preg_replace(",<script[^>]*></script>,i",'',$data['html']));

	return $data;
}