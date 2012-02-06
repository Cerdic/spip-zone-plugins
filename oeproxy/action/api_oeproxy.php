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

/**
 * Proxy oEmbed
 *  - redirige vers un endpoint oEmbed existant si connu
 *  - detecte une annonce du service oEmbed sur l'url de la page et redirige
 *  - utilise une methode interne basee sur une API du site, si existante
 *  - utiliser Readability en dernier ressort
 *
 * @param null|array $args
 * @return void
 */
function action_api_oeproxy_dist($args = null){
	static $methodes = array(
		',^https?://(www.)?twitpic.com/[^/]+,i' => 'twitpic',
		',https?://twitter.com/(?:#!/)?([^/#]+)/status(?:es)?/(\d+),i' => 'twitter',
		',\w+://([\w]+).tumblr.com/post/([^/]+).*,i' => 'tumblr',
		',\w+://([\w]+).wikipedia.org/wiki/([^/]+).*,i' => 'wikipedia',
		',^https?://.*facebook.com/(people/[^/]+/(\d+).*|([^/]+$)),i' => 'facebook',
		',^http://(?:www.)?imdb.com/title/([^/]+),i' => 'imdb',
#		',^http://(www.)?amazon.fr/[^/]+/[^/]+/([^/]+)/,i' => 'amazon',

	);
	static $support_formats = array(
		'xml',
		'json',
	);
	$force_reload=false;

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

	// verifier que le format est licite
	$format = (isset($args['format'])?$args['format']:'xml');
	if (!in_array($format,$support_formats))
		oeproxy_echec(501);

	// si l'url demandee a deja un endpoint connu, rediriger
	// ca ne sert a rien de refaire le travail
	if ($redirect = oeproxy_verifier_provider($url,$args)){
		include_spip('inc/headers');
		redirige_par_entete($redirect,'',301);
	}

	// recherche si methode oEmbed dediee
	$methode = 'default';
	foreach($methodes as $pattern => $action){
		if (preg_match($pattern,$url))
			$methode = $action;
	}

	$html = null;
	// si pas de methode dediee, rechercher une annonce de oEmbed dans la page
	if ($methode=='default'){
		// decouverte du service annonce dans la page
		include_spip('inc/distant');
		$html = recuperer_page_cache($url);
		if ($redirect = oeproxy_verifier_annonce($url, $args, $html)){
			include_spip('inc/headers');
			redirige_par_entete($redirect,'',301);
		}
	}

	// les deux seules options acceptees (les autres sont ignorees, d'ou qu'elles viennent)
	// + une option interne
	$options = array(
		'maxwidth' => (isset($args['maxwidth'])?intval($args['maxwidth']):null),
		'maxheight' => (isset($args['maxheight'])?intval($args['maxheight']):null),
		'force_reload' => $force_reload,
	);

	// appeler la methode proxy qui fait le job
	// et retourne le resultat sous forme de tableau
	$oeproxy = charger_fonction($methode,'oeproxy');
	$res = $oeproxy($url,$options,$html);


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


	$output = charger_fonction($format,'oembed/output');
	$output($res);

	flush();
	ob_flush();
	exit;
}

/**
 * Se declarer soi meme comme provider universel
 * @param array $providers
 * @return array
 */
function oeproxy_oembed_lister_providers($providers){

	$providers['http://*'] = 'oeproxy.api';
	$providers['https://*'] = 'oeproxy.api';

	return $providers;
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
			$redirect = parametre_url($redirect,'url',$url,'&');
			if (isset($args['maxheight']))
				$redirect = parametre_url($redirect,'maxheight',$args['maxheight'],'&');
			if (isset($args['maxwidth']))
				$redirect = parametre_url($redirect,'maxwidth',$args['maxwidth'],'&');
			if (isset($args['format']))
				$redirect = parametre_url($redirect,'format',$args['format'],'&');
		}
	}
	return $redirect;
}

function oeproxy_cite($url,$title,$html){
	$html = "<h4 class='title'><a href='$url'>".($title?$title:$url)."</a></h4>"
	        . "<blockquote class='spip' cite='$url'>$html</blockquote>";
	return $html;
}

/**
 * D�tecter les liens oembed dans le head d'une page web
 *
 * @param string $url
 * @param array $args
 * @param string $html
 * @return string
 */
function oeproxy_verifier_annonce($url, $args, $html=null) {
	$redirect = "";

	if (is_null($html)){
		// on recupere le contenu de la page
		include_spip('inc/distant');
		$html = recuperer_page_cache($url);
	}

	if ($html) {
		$providers = array();
		// types de liens oembed � d�tecter
		$linktypes = array(
			'application/json+oembed' => 'json',
			'text/json+oembed' => 'json', // ex de 500px
			'text/xml+oembed' => 'xml',
			'application/xml+oembed' => 'xml', // uniquement pour Vimeo
		);

		// on ne garde que le head de la page
		$head = substr($html,0,stripos($html,'</head>'));

		if (stripos($head,"+oembed")!==false // optimisation : eviter preg_match si rien qui ressemble
			AND preg_match_all('/<link([^<>]+)>/i', $head, $links)) {
			include_spip('inc/filtres');
			foreach ($links[0] as $link) {
				if (stripos($link,"+oembed")!==false){
					$type = extraire_attribut($link,'type');
					$href = extraire_attribut($link,'href');
					if (!empty($type)
					  AND isset($linktypes[$type])
					  AND !empty($href)) {
						$providers[$linktypes[$type]] = $href;
						if (!isset($args['format'])
						  OR $linktypes[$type]==$args['format'])
							break;
					}
				}
			}
		}

		if (count($providers)){
			if (!isset($args['format']))
				$redirect = reset($providers);
			elseif (isset($providers[$args['format']]))
				$redirect = $providers[$args['format']];
			if ($redirect){
				if (isset($args['maxheight']))
					$redirect = parametre_url($redirect,'maxheight',$args['maxheight'],'&');
				if (isset($args['maxwidth']))
					$redirect = parametre_url($redirect,'maxwidth',$args['maxwidth'],'&');
			}
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
			echo "501 Not Implemented : cannot return a response in the requested format";
			break;

		case 401:
			http_status('401');
			echo "401 Unauthorized : The specified URL contains a private (non-public) resource";
			break;

		case 404:
		default:
			http_status('404');
			echo "404 Not Found : no response for the requested url parameter";
			break;
	}

	flush();
	ob_flush();
	exit;
}


if (!defined('_DUREE_CACHE_HTML_PAGE'))
	define('_DUREE_CACHE_HTML_PAGE',3600);
/**
 * Recuperer une URL distante avec un cache file d'une 1H
 * et utilisation du not-modified-since au dela
 *
 * @param $url
 * @return bool|int|string
 */
function recuperer_page_cache($url){
	static $now = null;
	if (!$now) $now = time();

	$cache = md5($url);
	$dir = sous_repertoire(_DIR_CACHE,substr($cache,0,1));
	$cache = $dir."htmlcache-$cache.html";

	$date = 0;
	if (_VAR_MODE
		OR !file_exists($cache)
	  OR !$date=filemtime($cache)
	  OR $date<$now-_DUREE_CACHE_HTML_PAGE){

		include_spip('inc/distant');
		copie_locale($url,_VAR_MODE?'force':'modif',$cache);
	}

	lire_fichier($cache,$html);
	return $html;
}
