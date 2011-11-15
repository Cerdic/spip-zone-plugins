<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Proxy twitter pour transformer l'api json en oEmbed
 * http://planetozh.com/blog/2010/05/how-to-embed-a-tweet-in-wordpress-a-complete-oembed-tutorial/
 * Handle the oEmbed requests
 * The oEmbed request is something like:
 * yourblog.com/?action=oeproxy_twitter&url=http://twitter.com/ozh/statuses/123456&...
 *
 * ne supporte que le format json pour le moment
 *
 * @return void
 */
function action_oeproxy_twitter_dist(){

	$url = _request('url');
	if(!$url
	  OR !preg_match( ',https?://twitter.com/(?:#!/)?([^/#]+)/status(?:es)?/(\d+),i', $url, $matches ))
		die('Erreur : URL manquante ou incorrecte');

	if (_request('format')=='xml')
		die('Erreur : format xml non supporte');

	$author  = $matches[1];
	$tweetid = $matches[2];
	unset($matches);

	// From this point, we fetch content from Twitter and print a JSON string.
	// If something goes wrong (like, Twitter unreachable) then we'll simply print anything but JSON
	// and let oEmbed handle the response.
	// fetch http://api.twitter.com/1/statuses/show/$tweet.json
	$apiurl = 'http://api.twitter.com/1/statuses/show/'.$tweetid.'.json';
	include_spip('inc/distant');
	if (!$result = recuperer_page($apiurl))
		die( "could not fetch $apiurl" );

	// Check that JSON is well formed
	$result = trim($result);
	if (!$data = json_decode($result))
		die( "Data was not JSON" );
	#var_dump($data);
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

	// Put everything into a regular array
	$json = array(
		'version' => '1.0',
		'type'	=> 'rich',
		'width'   => 1337, // this parameter is mandatory according to the oEmbed specs, but we're not using it
		'height'  => 1337, // same.
		// Make up some HTML with the tweet info
		'html'	=> trim(recuperer_fond('modeles/oeproxy_twitter',$contexte)),
	);

	// Now output a JSON response
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	#header('Content-Disposition: attachment; filename="oembed.json"');
	echo json_encode( $json );
	exit;
}