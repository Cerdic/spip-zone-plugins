<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/distant');

/**
 * Proxy twitter pour transformer l'api json en oEmbed
 * http://planetozh.com/blog/2010/05/how-to-embed-a-tweet-in-wordpress-a-complete-oembed-tutorial/
 * Handle the oEmbed requests
 * The oEmbed request is something like:
 * yourblog.com/?action=oeproxy_twitter&url=http://twitter.com/ozh/statuses/123456&...
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_twitter_dist($url,$options,$html=null){

	if(!$url
	  OR !preg_match( ',https?://twitter.com/(?:#!/)?([^/#]+)/status(?:es)?/(\d+),i', $url, $matches )){
		spip_log('Url inconnue ou mal formee','oep_twitter');
		return 404;
	}

	$author  = $matches[1];
	$tweetid = $matches[2];
	unset($matches);

	// From this point, we fetch content from Twitter and print a JSON string.
	// If something goes wrong (like, Twitter unreachable) then we'll simply print anything but JSON
	// and let oEmbed handle the response.
	// fetch http://api.twitter.com/1/statuses/show/$tweet.json
	//$apiurl = 'http://api.twitter.com/1/statuses/show/'.$tweetid.'.json';
	$apiurl = 'https://api.twitter.com/1/statuses/oembed.json?id='.$tweetid;
	if (!$result = recuperer_page_cache($apiurl)){
		spip_log("could not fetch $apiurl",'oep_twitter');
		return 404;
	}

	// Check that JSON is well formed
	$result = trim($result);
	if (!$data = json_decode($result)){
		spip_log('Data was not JSON','oep_twitter');
		return 404;
	}


	$data->html = trim(preg_replace(",<script\b[^>]*></script>,Uims","",$data->html));


	#var_dump($data);

	// obsolete, plus possible
	/*
	// Now extract a few variables from the $data object
	#$created_at = date('d M Y g:i a', strtotime( $data->created_at ) );

	$text = $data->text;
	include_spip('inc/lien');
	include_spip('inc/filtres');
	include_spip('inc/texte');
	if (function_exists('traiter_raccourci_liens'))
		$text = traiter_raccourci_liens($text);

	// linker les hashtags
	$text = preg_replace(",(#\w+),","<a href='http://twitter.com/search?q=\\1'>\\1</a>",$text);
	// linker les users
	$text = preg_replace(",@(\w+),","<a href='http://twitter.com/\\1'>@\\1</a>",$text);

	$contexte = array(
		'url' => $url,
		'text' => $text,
		'created_at' => date('Y-m-d H:i:s',strtotime($data->created_at)),
		'source' => $data->source,
		'in_reply_to_screen_name' => $data->in_reply_to_screen_name,
		'in_reply_to_status_id' => $data->in_reply_to_status_id_str,
		'in_reply_to_user_id' => $data->in_reply_to_user_id_str,
		'screen_name' => $data->user->screen_name,
		'profile_image' => $data->user->profile_image_url,
		'name' => $data->user->name
	);
*/

	$result = array(
		// type (required)
		// The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'rich',

		// version (required)
		// The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
		// A text title, describing the resource.
		#'title' => '',

		// html (required)
		// The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => $data->html,

		// width (required)
		// The width in pixels required to display the HTML.
		'width' => 1337,

		// height (required)
		// The height in pixels required to display the HTML.
		'height' => 1337,

		// author_name (optional)
		// The name of the author/owner of the resource.
		'author_name' => $data->author_name,

		// author_url (optional)
		// A URL for the author/owner of the resource.
		// NIY
		'author_url' => $data->author_url,


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