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


function demo_link($url){
	$url = parametre_url($url,'dummy','','&'); // inverser le travail de entites_html
	if (!$url) return "";
	return url_absolue(parametre_url("oeproxy.api/",'url',$url));
}

function demo_oembed($url,$format='json'){
	$url = demo_link($url);
	if (!$url) return "";
	$url = parametre_url($url,'format',$format,'&');

	include_spip('inc/distant');
	return recuperer_page($url);
}
