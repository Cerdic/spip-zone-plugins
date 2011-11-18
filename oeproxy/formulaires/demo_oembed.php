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

function formulaires_demo_oembed_charger_dist(){
	return array(
		'url'=>_request('url'),
	);
}

function demo_link($url){
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

function formulaires_demo_oembed_traiter_dist(){


	return array('editable' => true);

}