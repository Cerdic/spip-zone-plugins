<?php

/**
 * takes a URL and attempts to return ...
 *
 * @param string $url The URL to the content that should be attempted to be embedded.
 * @param array $args Optional arguments. Usually passed from a shortcode.
 * @return bool|array False on failure, otherwise the array of response.
 */
function oembed_recuperer_data($url, $maxwidth = '', $maxheight = '', $format = 'json', $detecter_lien = 'non') {
	$provider = false;

	//if (!isset($discover))
	//	$args['discover'] = true;
	
	$provider = oembed_verifier_provider($url);

	//if ((!$provider) AND ($detecter_lien != 'non'))
	//	$provider = oembed_detecter_lien($url);

	//if ((!$provider) OR (!$data = oembed_fetch($provider, $url, $args)))
	//	return false;
	
	$url_json = parametre_url($provider,'url',$url,'&');
	$url_json = parametre_url($url_json,'maxwidth',$maxwidth,'&');
	$url_json = parametre_url($url_json,'maxheight',$maxheight,'&');
	$url_json = parametre_url($url_json,'format',$format,'&');

	// on recupere le contenu de la page
	include_spip('inc/distant');
	if ($data = recuperer_page($url_json)) {
		if ($format == 'json')
			return json_decode($data,true);
		if ($format == 'xml')
			return false;
	}

	return false;
}

/**
 * Verfier qu'une url est dans la liste des providers autorisés
 *
 * @param string $url l'url à tester
 * @return bool|string false si non, endpoint du provider si oui
 */
function oembed_verifier_provider($url) {
	$providers = sql_allfetsel('*', 'spip_oembed_providers');
	foreach ($providers as $p) {
		$regex = '/' . str_replace('\*', '(.+)', preg_quote($p['scheme'], '/')) . '/';
		if (preg_match($regex, $url)) {
			return $p['endpoint'];
		}
	}
	return false;
}

/**
 * Attempts to find oEmbed provider discovery <link> tags at the given URL.
 *
 * @param string $url The URL that should be inspected for discovery <link> tags.
 * @return bool|string False on failure, otherwise the oEmbed provider URL.
 */
function oembed_detecter_lien($url) {
	$providers = array();

	// on recupere le contenu de la page
	include_spip('inc/distant');
	if ($html = recuperer_page($url)) {
		
		// <link> types that contain oEmbed provider URLs
		$linktypes = array(
			'application/json+oembed' => 'json',
			'text/xml+oembed' => 'xml',
			'application/xml+oembed' => 'xml', // uniquement pour Vimeo
		);

		// Strip <body>
		$head = substr($html,0,stripos($html,'</head>'));

		// Do a quick check
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
					// Stop here if it's JSON (that's all we need)
					if ('json' == $linktypes[$type])
						break;
				}
			}
		}
	}

	// JSON is preferred to XML
	if (!empty($providers['json']))
		return $providers['json'];
	elseif (!empty($providers['xml']))
		return $providers['xml'];
	else
		return false;
}

?>