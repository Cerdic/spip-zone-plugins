<?php

// Merci WordPress :)
// http://core.trac.wordpress.org/browser/trunk/wp-includes/class-oembed.php

/**
 * Récupérer les données oembed d'une url
 *
 * @param string $url url de la page qui contient le document à récupérer avec oembed
 * @param int $maxwidth largeur max du document
 * @param int $maxheight hauteur max du document
 * @param string $format format à utiliser pour la requete oembed (json ou xml)
 * @param string $detecter_lien tenter la détection automatique de lien oembed dans la page indiquée
 * @return bool|array false si aucun retour ou erreur ; tableau des éléménents de la réponse oembed
 */
function oembed_recuperer_data($url, $maxwidth = '', $maxheight = '', $format = 'json', $detecter_lien = 'non') {
	$provider = false;
	
	$provider = oembed_verifier_provider($url);

	if ((!$provider) AND ($detecter_lien != 'non'))
		$provider = oembed_detecter_lien($url);
	
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
 * Vérfier qu'une url est dans la liste des providers autorisés
 *
 * @param string $url l'url à tester
 * @return bool|string false si non ; endpoint du provider si oui
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
		return $providers['json'];
	elseif (!empty($providers['xml']))
		return $providers['xml'];
	else
		return false;
}

?>