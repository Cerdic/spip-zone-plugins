<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister les providers connus
 * @return array
 */
function oembed_lister_providers() {

	// liste des providers par defaut

	// voir
	// http://oembed.com/
	// http://code.google.com/p/oohembed/source/browse/app/provider/endpoints.json
	// https://github.com/starfishmod/jquery-oembed-all/blob/master/jquery.oembed.js
	// https://github.com/panzi/oembedendpoints/blob/master/endpoints-simple.json
	// voir aussi http://embed.ly/providers qui donne les scheme mais pas les endpoint
	$providers = array(
		'http://*.youtube.com/watch*'             => 'https://www.youtube.com/oembed',
		'http://*.youtube.com/playlist*'          => 'https://www.youtube.com/oembed',
		'http://youtu.be/*'                       => 'https://www.youtube.com/oembed',
		'http://*.vimeo.com/*'                    => 'https://vimeo.com/api/oembed.json',
		'http://vimeo.com/*'                      => 'https://vimeo.com/api/oembed.json',
		'http://*.dailymotion.com/*'              => 'https://www.dailymotion.com/services/oembed',
		'http://dai.ly/*'                         => 'https://www.dailymotion.com/services/oembed',
		'http://*.flickr.com/*'                   => 'https://www.flickr.com/services/oembed/',
		'http://flickr.com/*'                     => 'https://www.flickr.com/services/oembed/',
		'http://flic.kr/*'                        => 'https://www.flickr.com/services/oembed/',
		'http://soundcloud.com/*'                 => 'https://soundcloud.com/oembed',
		'http://mixcloud.com/*'                   => 'https://mixcloud.com/oembed',
		'http://*.soundcloud.com/*'               => 'https://soundcloud.com/oembed',
		'http://*.mixcloud.com/*'                 => 'https://mixcloud.com/oembed',
		'http://*.slideshare.net/*/*'             => 'https://www.slideshare.net/api/oembed/2',
		'http://www.slideshare.net/*/*'           => 'https://www.slideshare.net/api/oembed/2',
		'http://instagr.am/*'                     => 'https://api.instagram.com/oembed',
		'http://*.instagr.am/*'                   => 'https://api.instagram.com/oembed',
		'http://instagram.com/*'                  => 'https://api.instagram.com/oembed',
		'http://*.instagram.com/*'                => 'https://api.instagram.com/oembed',
		'http://huffduffer.com/*/*'               => 'http://huffduffer.com/oembed',
		'http://nfb.ca/film/*'                    => 'http://www.nfb.ca/remote/services/oembed/',
		'http://dotsub.com/view/*'                => 'http://dotsub.com/services/oembed',
		'http://clikthrough.com/theater/video/*'  => 'http://clikthrough.com/services/oembed',
		'http://kinomap.com/*'                    => 'http://www.kinomap.com/oembed',
		'http://photobucket.com/albums/*'         => 'http://api.photobucket.com/oembed',
		'http://photobucket.com/groups/*'         => 'http://api.photobucket.com/oembed',
		'http://*.smugmug.com/*'                  => 'https://api.smugmug.com/services/oembed/',
		'http://meetup.com/*'                     => 'https://api.meetup.com/oembed',
		'http://meetup.ps/*'                      => 'http://api.meetup.com/oembed',
		'http://*.wordpress.com/*'                => 'https://public-api.wordpress.com/oembed/1.0/',
		'http://twitter.com/*/status/*'           => 'https://publish.twitter.com/oembed',
		'http://twitter.com/*/likes'              => 'https://publish.twitter.com/oembed',
		'http://twitter.com/*/lists/*'            => 'https://publish.twitter.com/oembed',
		'http://twitter.com/*/timelines/*'        => 'https://publish.twitter.com/oembed',
		'http://twitter.com/i/moments/*'          => 'https://publish.twitter.com/oembed',
		'http://techcrunch.com/*'                 => 'http://public-api.wordpress.com/oembed/1.0/',
		'http://wp.me/*'                          => 'http://public-api.wordpress.com/oembed/1.0/',
		'http://my.opera.com/*'                   => 'http://my.opera.com/service/oembed',
		'http://www.collegehumor.com/video/*'     => 'http://www.collegehumor.com/oembed.json',
		'http://imgur.com/*'                      => 'http://api.imgur.com/oembed',
		'http://*.imgur.com/*'                    => 'http://api.imgur.com/oembed',
		'http://*.onf.ca/*'                       => 'http://www.onf.ca/remote/services/oembed/',
		'http://vine.co/v/*'                      => 'https://vine.co/oembed.json',
		'http://*.tumblr.com/post/*'              => 'https://www.tumblr.com/oembed/1.0',
		'http://*.kickstarter.com/projects/*'     => 'https://www.kickstarter.com/services/oembed',
		'http://speakerdeck.com/*'                => 'https://speakerdeck.com/oembed.json',
		'http://issuu.com/*'                      => 'https://issuu.com/oembed',
		'http://www.facebook.com/*/posts/*'       => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/*/activity/*'    => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/*/photos/*'      => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/media/*'         => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/questions/*'     => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/notes/*'         => 'https://www.facebook.com/plugins/post/oembed.json/',
		'http://www.facebook.com/*/videos/*'      => 'https://www.facebook.com/plugins/video/oembed.json/',

		'http://egliseinfo.catholique.fr/*'       => 'http://egliseinfo.catholique.fr/api/oembed',

		#'https://gist.github.com/*' => 'http://github.com/api/oembed?format=json'
	);

	// pipeline pour permettre aux plugins d'ajouter/supprimer/modifier des providers
	$providers = pipeline('oembed_lister_providers', $providers);

	// merger avec la globale pour perso mes_options dans un site
	// pour supprimer un scheme il suffit de le renseigner avec un endpoint vide
	if (isset($GLOBALS['oembed_providers'])) {
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
 * @param bool $force_reload forcer le rechargement de l'oembed depuis la source sans utiliser le cache local
 * @return bool|array false si aucun retour ou erreur ; tableau des éléménents de la réponse oembed
 */
function oembed_recuperer_data($url, $maxwidth = null, $maxheight = null, $format = 'json', $detecter_lien = 'non', $force_reload = false) {
	static $cache = array();
	$provider = false;

	$provider = oembed_verifier_provider($url);
	if ((!$provider)
		and (($detecter_lien != 'non')
		or lire_config('oembed/detecter_lien', 'non') == 'oui')) {
		$provider = oembed_detecter_lien($url);
	}

	if (!$provider) {
		return false;
	}

	$data_url = url_absolue($provider['endpoint'], url_de_base());
	// certains oembed fournissent un endpoint qui contient deja l'URL, parfois differente de celle de la page
	if (!parametre_url($data_url, 'url')) {
		$data_url = parametre_url($data_url, 'url', $url, '&');
	}

	include_spip('inc/config');
	if (!$maxwidth) {
		$maxwidth = lire_config('oembed/maxwidth', '600');
	}
	if (!$maxheight) {
		$maxheight = lire_config('oembed/maxheight', '400');
	}

	$data_url = parametre_url($data_url, 'maxwidth', $maxwidth, '&');
	$data_url = parametre_url($data_url, 'maxheight', $maxheight, '&');
	$data_url = parametre_url($data_url, 'format', $format, '&');

	if (isset($provider['provider_name']) and $provider['provider_name']) {
		$provider_name = $provider['provider_name'];
	}
	else {
		// pre-traitement du provider si besoin
		$provider_name = explode('//', $provider['endpoint']);
		$provider_name = explode('/', $provider_name[1]);
		$provider_name = reset($provider_name);
	}
	$provider_name = preg_replace(',\W+,', '_', strtolower($provider_name));
	if ($oembed_endpoint_pretraite = charger_fonction("pretraite_$provider_name", 'oembed/input', true)) {
		$a = func_get_args();
		$args = array('url'=>array_shift($a));
		if (count($a)) {
			$args['maxwidth'] = array_shift($a);
		}
		if (count($a)) {
			$args['maxheight'] = array_shift($a);
		}
		if (count($a)) {
			$args['format'] = array_shift($a);
		}
		$args['endpoint'] = $provider['endpoint'];
		$data_url = $oembed_endpoint_pretraite($data_url, $args);
	}

	if (isset($cache[$data_url])) {
		return $cache[$data_url];
	}

	$oembed_cache = sous_repertoire(_DIR_CACHE, 'oembed').md5($data_url). '.'.$format;
	// si cache oembed dispo et pas de recalcul demande, l'utiliser (perf issue)
	if (!$force_reload and file_exists($oembed_cache) and _VAR_MODE !== 'recalcul') {
		lire_fichier($oembed_cache, $cache[$data_url]);
		$cache[$data_url]=unserialize($cache[$data_url]);
		return $cache[$data_url];
	}

	$oembed_recuperer_url = charger_fonction('oembed_recuperer_url', 'inc');
	$cache[$data_url] = $oembed_recuperer_url($data_url, $url, $format);
	// si une fonction de post-traitement est fourni pour ce provider+type, l'utiliser
	if ($cache[$data_url]) {
		$provider_name2= str_replace(' ', '_', strtolower($cache[$data_url]['provider_name']));
		$type = strtolower($cache[$data_url]['type']);
		// securisons le nom de la fonction (provider peut contenir n'importe quoi)
		$f1 = preg_replace(',\W,', '', "posttraite_{$provider_name2}_$type");
		$f2 = preg_replace(',\W,', '', "posttraite_{$provider_name2}");
		$f3 = preg_replace(',\W,', '', "posttraite_{$provider_name}_$type");
		$f4 = preg_replace(',\W,', '', "posttraite_{$provider_name}");
		if (
			$oembed_provider_posttraite = charger_fonction($f1, 'oembed/input', true)
			or $oembed_provider_posttraite = charger_fonction($f2, 'oembed/input', true)
			or $oembed_provider_posttraite = charger_fonction($f3, 'oembed/input', true)
			or $oembed_provider_posttraite = charger_fonction($f4, 'oembed/input', true) ) {
			$cache[$data_url] = $oembed_provider_posttraite($cache[$data_url], $url);
		}
		ecrire_fichier($oembed_cache, serialize($cache[$data_url]));
	}
	spip_log('infos oembed pour '.$url.' : '.var_export($cache[$data_url], true), 'oembed.'._LOG_DEBUG);

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
	if (strncmp($url, $GLOBALS['meta']['adresse_site'], strlen($GLOBALS['meta']['adresse_site'])) == 0) {
		return false;
	}
	$providers = oembed_lister_providers();
	foreach ($providers as $scheme => $endpoint) {
		$regex = '#' . str_replace('\*', '(.+)', preg_quote($scheme, '#')) . '#';
		$regex = preg_replace('|^#http\\\://|', '#https?\://', $regex);
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

	$oembed_recuperer_url = charger_fonction('oembed_recuperer_url', 'inc');
	// on recupere le contenu de la page
	if ($html = $oembed_recuperer_url($url, $url, 'html')) {
		// types de liens oembed à détecter
		$linktypes = array(
			'application/json+oembed' => 'json',
			'text/json+oembed' => 'json', // ex de 500px
			'text/xml+oembed' => 'xml',
			'application/xml+oembed' => 'xml', // uniquement pour Vimeo
		);

		// on ne garde que le head de la page
		$head = substr($html, 0, stripos($html, '</head>'));

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
				$type = extraire_attribut($link, 'type');
				$href = extraire_attribut($link, 'href');
				if (!empty($type) and !empty($linktypes[$type]) and !empty($href)) {
					$providers[$linktypes[$type]] = $href;
					// on a le json, ça nous suffit
					if ('json' == $linktypes[$type]) {
						break;
					}
				}
			}
		}
	}

	$res = array();

	// on préfère le json au xml
	if (!empty($providers['json'])) {
		$res['endpoint'] = $providers['json'];
	} elseif (!empty($providers['xml'])) {
		$res['endpoint'] = $providers['xml'];
	} else {
		return false;
	}

	// detecter certains providers specifiques : ex mastodon, chaque instance a son nom et on peut pas l'identifier par son URL
	if (strpos($html, '//github.com/tootsuite/mastodon') !== false or strpos($html, '//joinmastodon.org') !== false) {
		$res['provider_name'] = 'Mastodon';
	}

	return $res;
}


/**
 * Embarquer un lien oembed si possible
 * @param string $lien
 * @return string
 */
function oembed_embarquer_lien($lien) {
	static $base = null;

	$url = extraire_attribut($lien, 'href');
	$texte = null;
	if ($url
		and (
			oembed_verifier_provider($url)
			or (lire_config('oembed/detecter_lien', 'non') == 'oui'))
		) {
		if (is_null($base)) {
			$base = url_de_base();
		}
		// on embarque jamais un lien de soi meme car c'est une mise en abime qui donne le tourni
		// (et peut provoquer une boucle infinie de requetes http)
		if (strncmp($url, $base, strlen($base)) != 0) {
			$fond = recuperer_fond('modeles/oembed', array('url' => $url, 'lien' => $lien));
			if ($fond = trim($fond)) {
				$texte = $fond;
			}
		}
	}

	return $texte;
}
