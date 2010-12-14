<?php

# filtre pour Flickr avec l'iterateur DATA

function inc_flickr_to_array_dist($u) {
	/*
	$p = xml_parser_create();
	xml_parse_into_struct($p, $u, $vals, $index);
	xml_parser_free($p);

	var_dump($vals, $index);exit;

	include_spip('inc/xml');
	$u = spip_xml_parse($u);
	var_dump($u);exit;
	*/

	# JSONP
	#$u = preg_replace('/^\w+\((.*)\)$/', '\1', $u);

	include_spip('inc/distant');
	$u = recuperer_page($a = 'http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20flickr.photos.search%20where%20text=%22'.urlencode($u).'%22&format=json');

	$u = json_decode($u);

	$r = array();
	foreach( $u->query->results->photo as $ph ) {
		$ph = (array) $ph;
		$ph['url'] = 'http://farm'.$ph['farm'].'.static.flickr.com/'.$ph['server'].'/'.$ph['id'].'_'.$ph['secret'].'_s.jpg';
		$r[] = $ph;
	}
	return $r;
}

