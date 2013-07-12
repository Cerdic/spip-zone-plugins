<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Lister les providers connus
 * @return array
 */
function oembed_lister_providers(){

	// liste des providers par defaut

	// voir
	// http://oembed.com/
	// http://code.google.com/p/oohembed/source/browse/app/provider/endpoints.json
	// https://github.com/starfishmod/jquery-oembed-all/blob/master/jquery.oembed.js
	// https://github.com/panzi/oembedendpoints/blob/master/endpoints-simple.json
	// voir aussi http://embed.ly/providers qui donne les scheme mais pas les endpoint
	$providers = array(
		'http://*.youtube.com/watch*'    =>   'http://www.youtube.com/oembed',
		'http://youtu.be/*'              =>   'http://www.youtube.com/oembed',
		'http://blip.tv/file/*'          =>   'http://blip.tv/oembed/',
		'http://*.vimeo.com/*'           =>   'http://www.vimeo.com/api/oembed.json',
		'http://vimeo.com/*'             =>   'http://www.vimeo.com/api/oembed.json',
		'http://*.dailymotion.com/*'     =>   'http://www.dailymotion.com/api/oembed',
		'http://*.flickr.com/*'          =>   'http://www.flickr.com/services/oembed/',
		'http://flickr.com/*'            =>   'http://www.flickr.com/services/oembed/',
		'http://soundcloud.com/*'        =>   'http://soundcloud.com/oembed',
		'http://*.soundcloud.com/*'      =>   'http://soundcloud.com/oembed',
		'http://slideshare.net/*/*'      =>   'http://www.slideshare.net/api/oembed/2',
		'http://www.slideshare.net/*/*'  =>   'http://www.slideshare.net/api/oembed/2',
		'http://yfrog.com/*'         =>   'http://yfrog.com/api/oembed',
		'http://yfrog.*/*'         =>   'http://yfrog.com/api/oembed',
		'http://instagr.am/*'            =>   'http://api.instagram.com/oembed',
		'http://instagram.com/*'         =>   'http://api.instagram.com/oembed',
		'http://rd.io/*'                 =>   'http://www.rdio.com/api/oembed/',
		'http://rdio.com/*'              =>   'http://www.rdio.com/api/oembed/',
		'http://huffduffer.com/*/*'      =>   'http://huffduffer.com/oembed',
		'http://nfb.ca/film/*'           =>   'http://www.nfb.ca/remote/services/oembed/',
		'http://dotsub.com/view/*'       =>   'http://dotsub.com/services/oembed',
		'http://clikthrough.com/theater/video/*'=>'http://clikthrough.com/services/oembed',
		'http://kinomap.com/*'           =>   'http://www.kinomap.com/oembed',
		'http://photobucket.com/albums/*'=>   'http://photobucket.com/oembed/',
		'http://photobucket.com/groups/*'=>   'http://photobucket.com/oembed/',
		'http://smugmug.com/*/*'         =>   'http://api.smugmug.com/services/oembed/',
		'http://meetup.com/*'            =>   'http://api.meetup.com/oembed',
		'http://meetup.ps/*'             =>   'http://api.meetup.com/oembed',
		'http://*.wordpress.com/*'       =>   'http://public-api.wordpress.com/oembed/1.0/',
		'http://*.blogs.cnn.com/*'         =>   'http://public-api.wordpress.com/oembed/1.0/',
		'http://techcrunch.com/*'        =>   'http://public-api.wordpress.com/oembed/1.0/',
		'http://wp.me/*'                 =>   'http://public-api.wordpress.com/oembed/1.0/',
		'http://my.opera.com/*'           => 'http://my.opera.com/service/oembed',
		'http://*.viddler.com/*'         =>   'http://lab.viddler.com/services/oembed/',
		'http://www.collegehumor.com/video/*'=>'http://www.collegehumor.com/oembed.json',


		#'https://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
		#'http://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
		#'https://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',
		#'http://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',

		#'http://yfrog.ru|com.tr|it|fr|co.il|co.uk|com.pl|pl|eu|us)/*'         =>   'http://yfrog.com/api/oembed',
		#'https://gist.github.com/*' => 'http://github.com/api/oembed?format=json'
	);

	// pipeline pour permettre aux plugins d'ajouter/supprimer/modifier des providers
	$providers = pipeline('oembed_lister_providers',$providers);

	// merger avec la globale pour perso mes_options dans un site
	// pour supprimer un scheme il suffit de le renseigner avec un endpoint vide
	if (isset($GLOBALS['oembed_providers'])){
		$providers = array_merge($providers, $GLOBALS['oembed_providers']);
		// retirer les providers avec un endpoint vide
		$providers = array_filter($providers);
	}

	return $providers;
}

// Merci WordPress :)
// http://core.trac.wordpress.org/browser/trunk/wp-includes/class-oembed.php

/**
 * Récupérer les données oembed d'une url
 *
 * @param string $url url de la page qui contient le document à récupérer avec oembed
 * @param int $maxwidth largeur max du document
 *   null : la valeur configuree par defaut ou pour le provider est utilisee
 *   '' : pas de valeur max
 * @param int $maxheight hauteur max du document
 *   null : la valeur configuree par defaut ou pour le provider est utilisee
 *   '' : pas de valeur max
 * @param string $format format à utiliser pour la requete oembed (json ou xml)
 * @param string $detecter_lien tenter la détection automatique de lien oembed dans la page indiquée
 * @return bool|array false si aucun retour ou erreur ; tableau des éléménents de la réponse oembed
 */
function oembed_recuperer_data($url, $maxwidth = null, $maxheight = null, $format = 'json', $detecter_lien = 'non') {
	static $cache = array();
	$provider = false;
	
	$provider = oembed_verifier_provider($url);

	if ((!$provider)
	  AND (($detecter_lien != 'non') OR lire_config('oembed/detecter_lien','non')=='oui')) {
		$provider = oembed_detecter_lien($url);
	}

	if (!$provider)
		return false;
	
	$data_url = parametre_url(url_absolue($provider['endpoint'],url_de_base()),'url',$url,'&');
	include_spip('inc/config');
	if (!$maxwidth){
		$maxwidth = lire_config('oembed/maxwidth','600');
	}
	if (!$maxheight){
		$maxheight = lire_config('oembed/maxheight','400');
	}

	$data_url = parametre_url($data_url,'maxwidth',$maxwidth,'&');
	$data_url = parametre_url($data_url,'maxheight',$maxheight,'&');
	$data_url = parametre_url($data_url,'format',$format,'&');

	if (isset($cache[$data_url]))
		return $cache[$data_url];

	$oembed_cache = sous_repertoire(_DIR_CACHE,'oembed').md5($data_url).".".$format;
	// si cache oembed dispo et pas de recalcul demande, l'utiliser (perf issue)
	if (file_exists($oembed_cache) AND _VAR_MODE!=='recalcul'){
		lire_fichier($oembed_cache,$cache[$data_url]);
		$cache[$data_url]=unserialize($cache[$data_url]);
		return $cache[$data_url];
	}

	$cache[$data_url] = false;
	// on recupere le contenu de la page
	include_spip('inc/distant');
	spip_log('Requete oembed pour '.$url.' : '.$data_url,'oembed.'._LOG_DEBUG);
	if ($data = recuperer_page($data_url)) {
		spip_log('infos oembed brutes pour '.$url.' : '.$data,'oembed.'._LOG_DEBUG);
		if ($format == 'json')
			$cache[$data_url] = json_decode($data,true);
		// TODO : format xml
		//if ($format == 'xml')
		//	$cache[$data_url] = false;
	}


	// si une fonction de post-traitement est fourni pour ce provider+type, l'utiliser
	if ($cache[$data_url]){
		$provider_name= strtolower($cache[$data_url]['provider_name']);
		$type = strtolower($cache[$data_url]['type']);
		// securisons le nom de la fonction (provider peut contenir n'importe quoi)
		$f = preg_replace(",\W,","","posttraite_{$provider_name}_$type");
		if ($oembed_provider_posttraite = charger_fonction($f,'oembed/input',true))
			$cache[$data_url] = $oembed_provider_posttraite($cache[$data_url],$url);

		ecrire_fichier($oembed_cache,serialize($cache[$data_url]));
	}
	spip_log('infos oembed pour '.$url.' : '.var_export($cache[$data_url],true),'oembed.'._LOG_DEBUG);

	return $cache[$data_url];
}

/**
 * Vérfier qu'une url est dans la liste des providers autorisés
 *
 * @param string $url l'url à tester
 * @return bool|array
 *   false si non ; details du provider dans un tabeau associatif si oui
 */
function oembed_verifier_provider($url) {
	if (strncmp($url,$GLOBALS['meta']['adresse_site'],strlen($GLOBALS['meta']['adresse_site']))==0)
		return false;
	$providers = oembed_lister_providers();
	foreach ($providers as $scheme=>$endpoint) {
		$regex = '/' . str_replace('\*', '(.+)', preg_quote($scheme, '/')) . '/';
		if (preg_match($regex, $url)) {
			return array('endpoint' => $endpoint);
		}
	}
	return false;
}

/**
 * Détecter les liens oembed dans le head d'une page web
 *
 * @param string $url url de la page à analyser
 * @return bool|string false si pas de lien ; url du contenu oembed
 */
function oembed_detecter_lien($url) {
	$providers = array();

	// on recupere le contenu de la page
	include_spip('inc/distant');
	if ($html = recuperer_page($url)) {
		
		// types de liens oembed à détecter
		$linktypes = array(
			'application/json+oembed' => 'json',
			'text/json+oembed' => 'json', // ex de 500px
			'text/xml+oembed' => 'xml',
			'application/xml+oembed' => 'xml', // uniquement pour Vimeo
		);

		// on ne garde que le head de la page
		$head = substr($html,0,stripos($html,'</head>'));

		// un test rapide...
		$tagfound = false;
		foreach ($linktypes as $linktype => $format) {
			if (stripos($head, $linktype)) {
				$tagfound = true;
				break;
			}
		}
		
		if ($tagfound && preg_match_all('/<link([^<>]+)>/i', $head, $links)) {
			foreach ($links[0] as $link) {
				$type = extraire_attribut($link,'type');
				$href = extraire_attribut($link,'href');
				if (!empty($type) AND !empty($linktypes[$type]) AND !empty($href)) {
					$providers[$linktypes[$type]] = $href;
					// on a le json, ça nous suffit
					if ('json' == $linktypes[$type])
						break;
				}
			}
		}
	}

	// on préfère le json au xml
	if (!empty($providers['json']))
		return array('endpoint'=>$providers['json']);
	elseif (!empty($providers['xml']))
		return array('endpoint'=>$providers['xml']);
	else
		return false;
}

?>