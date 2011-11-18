<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_api_oeproxy_dist($args = null){
	static $methodes = array(

	);
	static $support_formats = array(
		'xml',
		'json',
	);

	if (is_null($args)){
		$args = array(
			'url' => _request('url'),
			'maxwidth' => _request('maxwidth'),
			'maxheight' => _request('maxheight'),
			'format' => _request('format'),
		);
	}

	// TODO : verification et sanitization de $args
	$url = (isset($args['url'])?trim($args['url']):'');
	if (!strlen($url) OR !preg_match(",^\w+://,",$url))
		oeproxy_echec(404);


	$format = (isset($args['format'])?$args['format']:'xml');
	if (!in_array($format,$support_formats))
		oeproxy_echec(501);
	unset($args['format']);

	// les deux seules options acceptees (les autres sont ignorees, d'ou qu'elles viennent)
	$options = array(
		'maxwidth' => (isset($args['maxwidth'])?intval($args['maxwidth']):null),
		'maxheight' => (isset($args['maxheight'])?intval($args['maxheight']):null),
	);



	// recherche et lancement de la methode oEmbed appropriee
	$methode = 'default';
	foreach($methodes as $pattern => $action){
		if (preg_match($pattern,$args['url']))
			$methode = $action;
	}

	// appeler la methode proxy qui fait le job
	// et retourne le resultat sous forme de tableau
	$oeproxy = charger_fonction($methode,'oeproxy');
	$res = $oeproxy($url,$args);


	// si la methode renvoie un entier
	// c'est un code d'erreur status
	if (is_int($res))
		oeproxy_echec($res);

	// renseigner les infos generiques
	// provider_name (optional)
	// The name of the resource provider.
	if (!isset($res['provider_name']))
		$res['provider_name'] = 'oeproxy_'.$methode;

	// provider_url (optional)
	// The url of the resource provider.
	// NIY
	#if (!isset($res['provider_url']))
	#	$res['provider_url'] = '';


	// cache_age (optional)
	// The suggested cache lifetime for this resource, in seconds. Consumers may choose to use this value or not.
	if (!isset($res['cache_age']))
		$res['cache_age'] = 7*24*3600;


	$output = charger_fonction($format,'oeoutput');
	$output($res);

	flush();
	ob_flush();
	exit;
}


/**
 * Generer une sortie en cas d'echec
 * @param int $status
 * @return void
 */
function oeproxy_echec($status=404){

	switch ($status){
		case 501:
			http_status('501');
			echo "501 Not Implemented";
			break;

		case 401:
			http_status('401');
			echo "401 Unauthorized";
			break;

		case 404:
		default:
			http_status('404');
			echo "404 Not Found";
			break;
	}

	flush();
	ob_flush();
	exit;
}