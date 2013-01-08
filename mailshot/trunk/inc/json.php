<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 *
 * Issu du plugin microblog
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


if ( !function_exists('json_decode') ){
	function json_decode($content, $assoc=false){
		include_spip('services/json');
		if ( $assoc ){
				$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		} else {
				$json = new Services_JSON;
		}
		return $json->decode($content);
	}
}

if ( !function_exists('json_encode') ){
	function json_encode($content){
		include_spip('services/json');
		$json = new Services_JSON;

		return $json->encode($content);
	}
}

include_once _DIR_RESTREINT.'inc/json.php';

?>