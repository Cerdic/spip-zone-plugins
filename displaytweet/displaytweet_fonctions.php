<?php
function d_tweet_twitt_propre ($twit) {
	// Convert URLs into hyperlinks
	$twit = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\" class='spip_out'>\\0</a>", $twit);
	// Convert usernames (@) into links 
	$twit = preg_replace("(@([a-zA-Z0-9\_]+))", "<a href=\"http://www.twitter.com/\\1\">\\0</a>", $twit);
	// Convert hash tags (#) to links 
	$twit = preg_replace('/(^|\s)#(\w+)/', '\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>', $twit);
	return $twit;
}
?>