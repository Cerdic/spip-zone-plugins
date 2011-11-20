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

include_spip('inc/filtres');
include_spip('inc/distant');

/**
 * Proxy tublr :
 * utilise l'api tublr pour le post
 * parse l'url pour la photo principale (un peu crappy mais pas trouve mieux)
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_wikipedia_dist($url,$options,$html=null){
	if (!$url
		OR !preg_match(',\w+://([\w]+).wikipedia.org/wiki/([^/]+).*,i',$url,$m))
		return 404;

	$sous = $m[1];
	$page = $m[2];
	unset($m);

	// ajouter &section=0 pour se limiter a la premiere section
	$api = "http://$sous.wikipedia.org/w/api.php?action=parse&page=$page&format=xml";
	$string = recuperer_page_cache($api);

	$xml = simplexml_load_string($string);

	$title = (string)$xml->parse->attributes()->displaytitle;
	$html = (string)$xml->parse->text;
	$html = liens_absolus($html,$url);

	$result = array(
		// type (required)
    // The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'rich',

		// version (required)
    // The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
    // A text title, describing the resource.
		'title' => $title,

		// html (required)
    // The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => oeproxy_cite($url,$title,$html),

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => ($options['width']?$options['width']:'300'),

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => ($options['height']?$options['height']:'100'),

		// author_name (optional)
    // The name of the author/owner of the resource.
		// NIY
		// 'author_name' => '',

		// author_url (optional)
    // A URL for the author/owner of the resource.
		// NIY
		// 'author_url' => '',


		// thumbnail_url (optional)
    // A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		// NIY
		// 'thumbnail_url' => '',

		// thumbnail_width (optional)
    // The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		// NIY
		// 'thumbnail_width' => '',

		// thumbnail_height (optional)
    // The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		// NIY
		// 'thumbnail_height' => '',

	);

	return $result;
}
