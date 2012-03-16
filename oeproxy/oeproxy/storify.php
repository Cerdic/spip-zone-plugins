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
 * Proxy storify :
 * utilise l'api storify pour reformater le contenu
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_storify_dist($url,$options,$html=null){

	// http://api.storify.com/v1/stories/user/story
	$segments = explode('/',$url);
	$story = array_pop($segments);
	$user = array_pop($segments);

	$url_api = "http://api.storify.com/v1/stories/$user/$story?per_page=50";

	// recuperer la story en json
	if (!$json_story = recuperer_page_cache($url_api)
	  OR !$json_story = json_decode($json_story,true))
		return 404;

	$nbs = array_sum($json_story['content']['stats']['elements']);
	$total_page = intval(ceil($nbs/50));
	// et les pages suivantes jusqu'a un max raisonable
	$page=2;
	do {
		if ($sub_story = recuperer_page_cache(parametre_url($url_api,'page',$page++,'&'))
			AND $sub_story = json_decode($sub_story,true)){
			$json_story['content']['elements'] = array_merge($json_story['content']['elements'],$sub_story['content']['elements']);
		}
	} while ($sub_story AND count($sub_story['content']['elements']) AND $page<$total_page);

	$title = $json_story['content']['title'];


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
		'html' => recuperer_fond('modeles/oeproxy/storify',$json_story),

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => ($options['width']?$options['width']:'300'),

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => ($options['height']?$options['height']:'100'),

		// author_name (optional)
    // The name of the author/owner of the resource.
		'author_name' => $json_story['author']['name'],

		// author_url (optional)
    // A URL for the author/owner of the resource.
		'author_url' => $json_story['author']['permalink'],


		// thumbnail_url (optional)
    // A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		//'thumbnail_url' => $contexte['picture'],

		// thumbnail_width (optional)
    // The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		//'thumbnail_width' => 50,

		// thumbnail_height (optional)
    // The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		//'thumbnail_height' => 50,

	);
#echo $result['html'];
#	die();
	return $result;
}