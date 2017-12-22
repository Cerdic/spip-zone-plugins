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
/**
 * googlefont_request
 *
 * prepare l'url sans passer par l'api , utilisé pour l'insertion dans le head
 *
 * @param $webfonts {array} font=>variantes
 * @param $subsets  si besoin une liste de subsets pour la forme mais inutile
 * @return $request url de requète 
 * 
*/
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

/**
 * google_font_search
 *
 * $fonts la liste des fonts récupérées via l'API
 * $search le motif de recherche sur item/family
*/
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

/**
 * googlefont_api_get
 *
 * retourne l'index complet de la typothèque via l'API
 *
 * @todo à stocker en copie locale ou dans /tmp ? 
*/
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