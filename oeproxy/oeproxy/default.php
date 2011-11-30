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

function oeproxy_default_dist($url,$options,$html=null){

	if (is_null($html)){
		$html = recuperer_page_cache($url);
	}

	if (!$html)
		return 404;

	// verifier si charset indique dans le <head>
	$charset = "";
	$p = stripos($html,'</head>');
	if (preg_match('/Content-Type([^;]+)(?:;\s*charset=([\w\d-]*))?/ims', substr($html,0,$p), $match)){
		// <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		$charset = strtolower($match[2]);
		include_spip('inc/charsets');
		$html = importer_charset($html, $charset);
	}

	$res = oeproxy_default_autoembed($url, $options, $html);
	if (!is_array($res))
		$res = oeproxy_default_readability($url, $options, $html);

	return $res;

}

function oeproxy_default_readability($url,$options,$html=null){

	include_spip('inc/readability');
	$res = readability_html($html,'array');

	// passer les liens en absolu !
	include_spip('inc/filtres_mini');
	$res['content'] = liens_absolus($res['content'],$url);

	$result = array(
		// type (required)
    // The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'rich',

		// version (required)
    // The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
    // A text title, describing the resource.
		'title' => $res['title'],

		// html (required)
    // The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => oeproxy_cite($url,$res['title'],$res['content']),

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


function oeproxy_default_autoembed($url,$options,$html=null){

	if (!include_spip('autoembed/AutoEmbed.class'))
		return 404;

	global $AutoEmbed_stubs;
	include_once _DIR_PLUGIN_OEPROXY . 'autoembed/stubs.php';

	$AE = new AutoEmbed();
	// load the embed source from a remote url
	if (!$AE->parseUrl($url, $html))
		return 404;


	$AE->setParam('autoplay','false');

	$attributs = $AE->getObjectAttribs();
	$w = $attributs["width"];
	$h = $attributs["height"];

	$rapport = 1.0;
	if (isset($options['maxwidth'])
		AND $w > $options['maxwidth'])
		$rapport = $w / $options['maxwidth'];
	if (isset($options['maxheight'])
		AND $h > $options['maxheight'])
		$rapport = min($h / $options['maxheight'],$rapport);

	if ($rapport<1.0){
		$w = round($w / $rapport);
		$h = round($h / $rapport);

		$AE->setWidth($w);
		$AE->setHeight($h);
	}

	$embed = $AE->getEmbedCode();
	#$vignette = $AE->getImageURL();

	#$source = $AE->getStub("title");
	#$code_ae = "<div class='oembed-container'>".$embed."</div>";

	$p = stripos($html,'</head>');
	include_spip('inc/filtres');
	$title = extraire_balise($p?substr($html,0,$p):$html,"title");
	$title = strip_tags($title);


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
		'html' => oeproxy_cite($url,$title,$embed),

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => $w,

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => $h,

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