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

define('_provider_name_prefixe','oeproxy_');

function action_api_oeproxy_dist($args = null){
	static $methodes = array(
		',^https?://(www.)?twitpic.com/[^/]+,i' => 'twitpic',
		',https?://twitter.com/(?:#!/)?([^/#]+)/status(?:es)?/(\d+),i' => 'twitter',

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

	// si l'url demandee a deja un endpoint connu, rediriger
	// ca ne sert a rien de refaire le travail
	if ($redirect = oeproxy_verifier_provider($url,$args)){
		include_spip('inc/headers');
		redirige_par_entete($redirect,'',301);
	}

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
		if (preg_match($pattern,$url))
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
		$res['provider_name'] = _provider_name_prefixe.$methode;

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
 * Verifier si l'url demandee ne peut pas etre servie par un provider connu
 * et dans ce cas on redirige simplement en 301
 * @param string $url
 * @param array $args
 * @return string
 */
function oeproxy_verifier_provider($url,$args){
	$redirect = '';
	if(include_spip('inc/oembed')
		AND function_exists('oembed_verifier_provider')
		AND $provider = oembed_verifier_provider($url)){

		// ne rediriger que si le provider est bien un service externe
		if (preg_match(",^\w+://,",$provider['endpoint'])){
			$redirect = $provider['endpoint'];
			$redirect = parametre_url($redirect,'url',$url);
			if (isset($args['maxheight']))
				$redirect = parametre_url($redirect,'maxheight',$args['maxheight']);
			if (isset($args['maxwidth']))
				$redirect = parametre_url($redirect,'maxwidth',$args['maxwidth']);
			if (isset($args['format']))
				$redirect = parametre_url($redirect,'format',$args['format']);
		}
	}
	return $redirect;
}

/**
 * Generer une sortie en cas d'echec
 * @param int $status
 * @return void
 */
function oeproxy_echec($status=404){

	switch ($status){
		case 501:
			header("Status: 501 Not Implemented");
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