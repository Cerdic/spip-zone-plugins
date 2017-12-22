<?php
/*
 * Plugin Webfonts2
 * (c) 2016
 * Distribue sous licence GPL
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function googlefont_request($webfonts,$subsets=''){
	$subset = '&subset=' ;
	(strlen($subsets)) ? $subset .= $subsets : $subset = '';
	foreach($webfonts as $font){
		$variants = implode(',',$font['variants']);
		$fonts[] = urlencode($font['family']).':'.$variants;	
	}	
	$fonts = implode('|',$fonts);
	$request = "https://fonts.googleapis.com/css?family=$fonts".$subset;
	return $request;
}


function google_font_search($fonts, $search){
	$res = array();
	foreach($fonts['items'] as $item){
		( preg_match('/' . trim($search) . '/i', $item['family']) ) ? $res[] = $item : false;
	}
	
	return $res;
}




function lister_webfonts(){
	$fonts = pipeline('fonts_list',array(
		'args'=>array(),
		'data'=>$fonts
	));
	return $fonts;
}

function googlefont_api_get($api_key,$sort=false,$category=false){
	// Requète en GET sur //https://www.googleapis.com/webfonts/v1/webfonts?key=_GOOGLE_API_KEY
	$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key='.$api_key;
	(strlen($sort)) ? $url .= '&sort='.$sort : $sort = false ;
	(strlen($category)) ? $url .= '&category='.$category : $category = false;	
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);
	curl_close($ch);
	$googlefonts = json_decode($result, true);
	
	return $googlefonts;
}