<?php
/*
 * Squelette
 * (c) 2016 
 * Distribue sous licence GPL
 *
 * @url - http://programmer.spip.net/-Formulaires-35-
 * http://marcimat.magraine.net/Les-formulaires-CVT-de-SPIP
 * 
 *
 */

function formulaires_selectionner_webfonts_charger_dist(){
	//tester si la google api_key est definie
	//sinon config
	$valeurs['googlefonts_api'] = _GOOGLE_API_KEY ;

    $valeurs = array(
		'font_list'=>_request('font_list'),
		'font_search'=>_request('font_search'),
		'sort'=>_request('sort'),
		'preview_text'=>_request('preview_text'),
		'category'=>_request('category')
    );
	return $valeurs;
}

function formulaires_selectionner_webfonts_verifier_dist(){
	$erreurs = array();
	
	if (count($erreurs)) {
		$erreurs['message_erreur'] = "Une erreur est présente dans votre saisie";
	}
	return $erreurs;
}

function formulaires_selectionner_webfonts_traiter_dist(){
	
    // Requète en GET sur //https://www.googleapis.com/webfonts/v1/webfonts?key=_GOOGLE_API_KEY
	$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key='._GOOGLE_API_KEY;
	if($sort = _request('sort'))
		$url .= '&sort='.$sort;
	if($category= _request('category'))
		$url .= '&category='.$category;
	//if($preview_text=_request('preview_text'))
	//	$url .= '&text='.$preview_text;
		
		
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
	//if($font_search=_request('font_search')){
	//	
	//	foreach($googlefonts['items'] as $item){
	//		var_dump(strstr($item['family'], $font_search));
	//		//var_dump(array_filter($item, function($k,$v) {
	//		//		if($family = stristr($v['family'],$font_search) )
	//		//			return  $v['family'] ;
	//		//}, ARRAY_FILTER_USE_BOTH));
	//	}
	//
	//}
	set_request('font_list', $googlefonts);
	set_request('font_search',_request('font_search'));

	$res = array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
	
	return $res;
}





?>