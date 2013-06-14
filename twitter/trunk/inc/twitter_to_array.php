<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function inc_twitter_to_array_dist($url, $options = array()) {

	$url = parse_url($url);

	$command = $url['path'];
	$params = array();
	parse_str($url['query'],$params);

	if (!function_exists('twitter_api_call'))
		include_spip("inc/twitter");

	$res = twitter_api_call($command,'get',$params,$options);

	return $res;
}
