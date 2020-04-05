<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_api_get_title_dist($arg = null) {

	$arg = _request('url');
	$url = $arg;
	$html = file_get_contents_curl($url);

	preg_match('/<title>(.+)<\/title>/', $html, $matches);
	$title = $matches[1];

	//var_dump($arg);
	echo json_encode(array("url" => $url, "title" => $title));
}

function file_get_contents_curl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

