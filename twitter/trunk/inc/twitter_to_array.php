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

	if (!function_exists('microblog_twitter_api'))
		include_spip("microblog_fonctions");

	$tokens = isset($options['tokens'])?$options['tokens']:null;

	$res = microblog_twitter_api($command,'get',$params,'',$tokens);

	return $res;
}
