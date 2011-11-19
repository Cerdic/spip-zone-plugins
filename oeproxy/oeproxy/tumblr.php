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
function oeproxy_tumblr_dist($url,$options,$html=null){

	if (!$url
		OR !preg_match(',\w+://([\w]+).tumblr.com/post/([^/]+).*,i',$url,$m))
		return 404;

	$blog = $m[1];
	$id = $m[2];
	unset($m);

	$api = "http://$blog.tumblr.com/api/read?id=$id";
	$string = recuperer_page_cache($api);

	$author = "";
	if (preg_match(",<tumblelog[^>]*>,Uims",$string,$m)){
		$author = extraire_attribut($m[0],'title');
	}

	$xml = simplexml_load_string($string);
	$post = $xml->posts->post;
#var_dump($post);
	$childs = array();
	foreach($post->children() as $name=>$value)
		if (!isset($childs[$name]))
			$childs[$name]=(string)$value;

	/*
	 * http://www.tumblr.com/docs/fr/api
	 *
	<post type="regular" ... >
            <regular-title>...</regular-title>
            <regular-body>...</regular-body>
        </post>
        <post type="link" ... >
            <link-text>...</link-text>
            <link-url>...</link-url>
        </post>
        <post type="quote" ... >
            <quote-text>...</quote-text>
            <quote-source>...</quote-source>
        </post>
        <post type="conversation" ... >
            <conversation-title>...</conversation-title>
            <conversation-text>...</conversation-text>
            <conversation>
                <line name="..." label="...">...</line>
                <line name="..." label="...">...</line>
                ...
            </conversation>
        </post>
        <post type="photo" ... >
            <photo-caption>...</photo-caption>
            <photo-url max-width="500">...</photo-url>
            <photo-url max-width="400">...</photo-url>
            ...
        </post>
        <post type="video" ... >
            <video-caption>...</video-caption>
            <video-source>...</video-source>
            <video-player>...</video-player>
        </post>
        <post type="audio" ... >
            <audio-caption>...</audio-caption>
            <audio-player>...</audio-player>
        </post>
        <post type="answer" ... >
            <question>...</question>
            <answer>...</answer>
        </post>

	*/

	$type = 'rich';
	switch($post->attributes()->type){
		case "regular":
			$title = $childs['regular-title'];
			$html = $childs['regular-body'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "link":
			$title = '<a href="'.$childs['link-url'].'">'.$childs['link-text'].'</a>';
			$html = $childs['link-description'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "quote":
			$title = $childs['quote-source'];
			$html = $childs['quote-text'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "conversation":
			$title = $childs['conversation-title'];
			$html = $childs['conversation-text'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "photo":
			$title = $childs['photo-caption'];
			$html = $childs['photo-url'];
			$html = ($title?"<h3>$title</h3>":"")."<div><img src='$html' /></div>";
			break;
		case "video":
			$title = $childs['video-caption'];
			$html = $childs['video-player'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "audio":
			$title = $childs['audio-caption'];
			$html = $childs['audio-player'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
		case "answer":
			$title = $childs['question'];
			$html = $childs['answer'];
			$html = ($title?"<h3>$title</h3>":"")."<div>$html</div>";
			break;
	}


	$result = array(
		// type (required)
		// The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => $type,

		// version (required)
		// The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
		// A text title, describing the resource.
		'title' => $title,

		// html (required)
		// The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => $html,

		// width (required)
		// The width in pixels required to display the HTML.
		'width' => 1337,

		// height (required)
		// The height in pixels required to display the HTML.
		'height' => 1337,

		// author_name (optional)
		// The name of the author/owner of the resource.
		'author_name' => $author,

		// author_url (optional)
		// A URL for the author/owner of the resource.
		// NIY
		'author_url' => "http://$blog.tumblr.com/",


		// thumbnail_url (optional)
		// A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		#'thumbnail_url' => '',

		// thumbnail_width (optional)
		// The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		#'thumbnail_width' => '',

		// thumbnail_height (optional)
		// The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		#'thumbnail_height' => '',
	);

	return $result;
}