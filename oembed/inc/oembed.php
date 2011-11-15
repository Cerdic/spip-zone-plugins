<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

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
	if (is_null($maxwidth)){
		$maxwidth = lire_config('oembed/maxwidth','600');
	}
	if (is_null($maxheight)){
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
		if ($oembed_provider_posttraite = charger_fonction("posttraite_{$provider_name}_$type",'oembed',true))
			$cache[$data_url] = $oembed_provider_posttraite($cache[$data_url]);

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
	$providers = sql_allfetsel('*', 'spip_oembed_providers');
	foreach ($providers as $p) {
		$regex = '/' . str_replace('\*', '(.+)', preg_quote($p['scheme'], '/')) . '/';
		if (preg_match($regex, $url)) {
			return $p;
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
		return array('endpoint'=>$providers['json']);
	elseif (!empty($providers['xml']))
		return array('endpoint'=>$providers['xml']);
	else
		return false;
}

?>